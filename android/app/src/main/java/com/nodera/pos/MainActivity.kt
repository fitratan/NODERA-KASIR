package com.nodera.pos

import android.Manifest
import android.annotation.SuppressLint
import android.content.Context
import android.content.Intent
import android.content.SharedPreferences
import android.content.pm.ActivityInfo
import android.content.pm.PackageManager
import android.net.Uri
import android.os.Build
import android.os.Bundle
import android.view.View
import android.view.WindowInsets
import android.view.WindowInsetsController
import android.webkit.JavascriptInterface
import android.webkit.PermissionRequest
import android.webkit.WebChromeClient
import android.webkit.WebResourceError
import android.webkit.WebResourceRequest
import android.webkit.WebSettings
import android.webkit.WebView
import android.webkit.WebViewClient
import android.widget.EditText
import androidx.activity.OnBackPressedCallback
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.core.app.ActivityCompat
import androidx.core.content.ContextCompat
import androidx.lifecycle.lifecycleScope
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.async
import kotlinx.coroutines.awaitAll
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import java.net.HttpURLConnection
import java.net.InetAddress
import java.net.InetSocketAddress
import java.net.NetworkInterface
import java.net.Socket
import java.net.URL

class MainActivity : AppCompatActivity() {

    private lateinit var webView: WebView
    private lateinit var prefs: SharedPreferences
    private var pendingPermissionRequest: PermissionRequest? = null

    companion object {
        private const val PREFS_NAME = "NoderaPosPrefs"
        private const val KEY_SERVER_URL = "server_url"
        private const val DEFAULT_PORT = 8088
        private const val PERMISSION_REQUEST_CAMERA = 101
    }

    @SuppressLint("SetJavaScriptEnabled")
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        try {
            prefs = getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE)

            // 1. Orientasi dinamis (Portrait & Landscape sesuai sensor perangkat)
            try {
                requestedOrientation = ActivityInfo.SCREEN_ORIENTATION_UNSPECIFIED
            } catch (_: Throwable) {}

            // 2. Mode Immersive Fullscreen (Menyembunyikan status bar & navigasi)
            hideSystemBars()

            // 3. Setup WebView
            webView = WebView(this)
            setContentView(webView)

            setupWebViewSettings()
            setupWebViewClients()

            // 4. Handle Tombol Back Fisik / Swipe Gesture Android
            onBackPressedDispatcher.addCallback(this, object : OnBackPressedCallback(true) {
                override fun handleOnBackPressed() {
                    if (this@MainActivity::webView.isInitialized && webView.canGoBack()) {
                        webView.goBack()
                    } else {
                        if (!isFinishing && !isDestroyed) {
                            AlertDialog.Builder(this@MainActivity)
                                .setTitle("Keluar Aplikasi")
                                .setMessage("Apakah Anda yakin ingin keluar dari Kasir Nodera POS?")
                                .setPositiveButton("Ya") { _, _ -> finish() }
                                .setNegativeButton("Tidak", null)
                                .show()
                        }
                    }
                }
            })

            // 5. Load Target Server URL
            loadInitialServer()
        } catch (t: Throwable) {
            android.util.Log.e("NoderaPOS", "Fatal onCreate error", t)
        }
    }

    @SuppressLint("SetJavaScriptEnabled")
    private fun setupWebViewSettings() {
        val settings: WebSettings = webView.settings
        settings.javaScriptEnabled = true
        settings.domStorageEnabled = true
        settings.databaseEnabled = true
        settings.loadWithOverviewMode = true
        settings.useWideViewPort = true
        settings.allowFileAccess = true
        settings.allowContentAccess = true
        settings.mediaPlaybackRequiresUserGesture = false

        // Bridge JS ke Android Native
        webView.addJavascriptInterface(PosBridgeInterface(), "AndroidPos")
    }

    private fun setupWebViewClients() {
        webView.webViewClient = object : WebViewClient() {
            override fun onReceivedError(
                view: WebView?,
                request: WebResourceRequest?,
                error: WebResourceError?
            ) {
                super.onReceivedError(view, request, error)
                if (request?.isForMainFrame == true) {
                    val failingUrl = request.url?.toString() ?: ""
                    // Cegah loop tak hingga jika URL gagal adalah internal / data / blank
                    if (failingUrl.isEmpty() || failingUrl.startsWith("data:") || failingUrl == "about:blank" || failingUrl.contains("nodera.local")) {
                        return
                    }
                    showOfflineErrorPage(failingUrl)
                }
            }

            override fun shouldOverrideUrlLoading(view: WebView?, request: WebResourceRequest?): Boolean {
                val url = request?.url?.toString() ?: return false
                return handleCustomScheme(url)
            }

            @Deprecated("Deprecated in Java")
            @Suppress("DEPRECATION")
            override fun shouldOverrideUrlLoading(view: WebView?, url: String?): Boolean {
                val targetUrl = url ?: return false
                return handleCustomScheme(targetUrl)
            }
        }

        // WebChromeClient: Grant camera permission on-demand for web barcode scanner
        webView.webChromeClient = object : WebChromeClient() {
            override fun onPermissionRequest(request: PermissionRequest?) {
                if (request == null) return
                if (ContextCompat.checkSelfPermission(this@MainActivity, Manifest.permission.CAMERA) == PackageManager.PERMISSION_GRANTED) {
                    runOnUiThread { request.grant(request.resources) }
                } else {
                    pendingPermissionRequest = request
                    ActivityCompat.requestPermissions(
                        this@MainActivity,
                        arrayOf(Manifest.permission.CAMERA),
                        PERMISSION_REQUEST_CAMERA
                    )
                }
            }
        }

        // Long click untuk buka dialog ganti IP server kapan saja
        webView.setOnLongClickListener {
            showServerConfigDialog(null)
            true
        }
    }

    private fun handleCustomScheme(url: String): Boolean {
        if (url.startsWith("http://") || url.startsWith("https://")) {
            return false // Load normally in WebView
        }
        try {
            val intent = if (url.startsWith("intent:")) {
                Intent.parseUri(url, Intent.URI_INTENT_SCHEME)
            } else {
                Intent(Intent.ACTION_VIEW, Uri.parse(url))
            }
            if (intent != null) {
                startActivity(intent)
                return true
            }
        } catch (_: Exception) {
            // Jika aplikasi RawBT printer belum terpasang di Android kasir
            if (url.contains("ru.a402d.rawbtprinter")) {
                try {
                    startActivity(Intent(Intent.ACTION_VIEW, Uri.parse("market://details?id=ru.a402d.rawbtprinter")))
                    return true
                } catch (_: Exception) {
                    startActivity(Intent(Intent.ACTION_VIEW, Uri.parse("https://play.google.com/store/apps/details?id=ru.a402d.rawbtprinter")))
                    return true
                }
            }
        }
        return true
    }

    private fun loadInitialServer() {
        val savedUrl = prefs.getString(KEY_SERVER_URL, null)
        if (!savedUrl.isNullOrEmpty()) {
            webView.loadUrl(savedUrl)
        } else {
            // Jika belum ada server tersimpan:
            val localIp = getDeviceIpAddress()
            val defaultUrl = if (isEmulator()) {
                "http://10.0.2.2:$DEFAULT_PORT"
            } else if (localIp != null) {
                // Tampilkan halaman setup awal dengan saran subnet
                val subnet = localIp.substringBeforeLast(".")
                "http://$subnet.1:$DEFAULT_PORT"
            } else {
                "http://192.168.1.100:$DEFAULT_PORT"
            }

            if (isEmulator()) {
                prefs.edit().putString(KEY_SERVER_URL, defaultUrl).apply()
                webView.loadUrl(defaultUrl)
            } else {
                showOfflineErrorPage(defaultUrl, isFirstRun = true)
            }
        }
    }

    private fun showOfflineErrorPage(targetUrl: String, isFirstRun: Boolean = false) {
        val deviceIp = getDeviceIpAddress() ?: "Tidak terhubung WiFi"
        val cleanTarget = targetUrl.ifEmpty { "http://192.168.1.100:8088" }

        val badgeText = if (isFirstRun) "PENGATURAN AWAL" else "SERVER BELUM TERHUBUNG"
        val badgeColor = if (isFirstRun) "#0D9488" else "#EF4444"
        val descText = if (isFirstRun) {
            "Selamat datang di <b>Nodera Kasir POS</b>. Silakan masukkan alamat IP komputer kasir (PC/laptop) yang menjalankan <code>NoderaKasir.exe</code>."
        } else {
            "Gagal menghubungkan ke <code>$cleanTarget</code>. Pastikan aplikasi kasir di PC sudah aktif dan HP berada di jaringan WiFi yang sama."
        }

        val html = """
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Nodera Kasir POS</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
  body { background: #052A4E; color: #FFFFFF; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
  .card { background: #0B3B66; border: 1px solid rgba(255,255,255,0.14); border-radius: 16px; padding: 26px 22px; max-width: 440px; width: 100%; box-shadow: 0 20px 40px rgba(0,0,0,0.5); text-align: center; }
  .logo-box { width: 68px; height: 68px; margin: 0 auto 14px; border-radius: 14px; background: #16191E; display: flex; align-items: center; justify-content: center; border: 2px solid #0D9488; }
  .badge { display: inline-block; background: ${badgeColor}22; color: ${badgeColor}; border: 1px solid ${badgeColor}55; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 999px; margin-bottom: 12px; letter-spacing: 0.05em; }
  h1 { font-size: 19px; font-weight: 700; margin-bottom: 8px; }
  .info-box { background: rgba(5, 42, 78, 0.6); border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; padding: 12px 14px; margin-bottom: 18px; text-align: left; font-size: 12.5px; line-height: 1.5; color: #CBD5E1; }
  .info-box code { color: #5EEAD4; background: rgba(0,0,0,0.3); padding: 2px 6px; border-radius: 4px; font-family: monospace; font-size: 12px; }
  .ip-info { font-size: 11.5px; color: #94A3B8; margin-bottom: 16px; text-align: left; }
  .ip-info span { color: #5EEAD4; font-weight: 600; }
  .form-group { text-align: left; margin-bottom: 14px; }
  label { font-size: 12px; font-weight: 600; color: #E2E8F0; display: block; margin-bottom: 6px; }
  input { width: 100%; background: #052A4E; border: 1.5px solid #1E4E79; color: #FFFFFF; font-size: 15px; padding: 12px 14px; border-radius: 10px; outline: none; transition: border-color 0.2s; }
  input:focus { border-color: #0D9488; }
  .btn-primary { width: 100%; background: #0D9488; color: #FFFFFF; font-size: 14.5px; font-weight: 700; padding: 13px; border-radius: 10px; border: none; cursor: pointer; margin-bottom: 10px; transition: background 0.2s; }
  .btn-primary:active { background: #0F766E; }
  .btn-secondary { width: 100%; background: transparent; color: #5EEAD4; font-size: 13px; font-weight: 600; padding: 10px; border-radius: 10px; border: 1px solid rgba(94, 234, 212, 0.35); cursor: pointer; }
  .btn-secondary:active { background: rgba(94, 234, 212, 0.1); }
  .status-scan { font-size: 12px; color: #F59E0B; margin-top: 10px; min-height: 18px; }
  .footer-hint { font-size: 11px; color: #64748B; margin-top: 16px; line-height: 1.4; }
</style>
</head>
<body>
  <div class="card">
    <div class="logo-box">
      <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#0D9488" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
        <line x1="8" y1="21" x2="16" y2="21"></line>
        <line x1="12" y1="17" x2="12" y2="21"></line>
      </svg>
    </div>
    <div class="badge">$badgeText</div>
    <h1>Koneksi Server Kasir</h1>
    <div class="info-box">$descText</div>
    
    <div class="ip-info">IP Perangkat Ini: <span>$deviceIp</span></div>

    <div class="form-group">
      <label>Alamat IP Server Kasir (PC/Laptop):</label>
      <input type="text" id="serverInput" value="$cleanTarget" placeholder="192.168.1.100:8088">
    </div>

    <button class="btn-primary" onclick="submitConnection()">Hubungkan ke Kasir</button>
    <button class="btn-secondary" id="btnScan" onclick="startScan()">🔍 Cari Server Otomatis (WiFi)</button>
    
    <div class="status-scan" id="statusScan"></div>

    <div class="footer-hint">
      Tips: Tekan dan tahan (long press) layar kasir kapan saja untuk mengubah IP server.
    </div>
  </div>

  <script>
    function submitConnection() {
      var val = document.getElementById('serverInput').value;
      if (!val || val.trim() === '') {
        alert('Mohon masukkan alamat IP server kasir');
        return;
      }
      if (window.AndroidPos) {
        window.AndroidPos.connectToServer(val);
      }
    }

    function startScan() {
      var btn = document.getElementById('btnScan');
      var st = document.getElementById('statusScan');
      btn.disabled = true;
      btn.innerText = '⏳ Sedang memindai jaringan WiFi...';
      st.innerText = 'Mencari server kasir di port 8088...';
      if (window.AndroidPos) {
        window.AndroidPos.scanSubnet();
      }
    }

    function onServerFound(ip) {
      document.getElementById('serverInput').value = ip;
      document.getElementById('statusScan').innerText = '✅ Ditemukan: ' + ip;
      document.getElementById('btnScan').disabled = false;
      document.getElementById('btnScan').innerText = '🔍 Cari Server Otomatis (WiFi)';
      setTimeout(function() { submitConnection(); }, 800);
    }

    function onScanFinished(found) {
      document.getElementById('btnScan').disabled = false;
      document.getElementById('btnScan').innerText = '🔍 Cari Server Otomatis (WiFi)';
      if (!found) {
        document.getElementById('statusScan').innerText = '⚠️ Server kasir tidak terdeteksi otomatis. Silakan masukkan IP PC secara manual.';
      }
    }
  </script>
</body>
</html>
        """.trimIndent()

        runOnUiThread {
            webView.loadDataWithBaseURL(null, html, "text/html", "UTF-8", null)
        }
    }

    private fun showServerConfigDialog(errorMessage: String? = null) {
        if (isFinishing || isDestroyed) return

        val currentUrl = prefs.getString(KEY_SERVER_URL, "") ?: ""
        val input = EditText(this).apply {
            hint = "http://192.168.1.100:8088"
            setText(currentUrl)
            setSelection(text.length)
        }

        AlertDialog.Builder(this)
            .setTitle("Konfigurasi Server Kasir")
            .setMessage(errorMessage ?: "Masukkan alamat IP server kasir Nodera (PC/Laptop):")
            .setView(input)
            .setPositiveButton("Hubungkan") { _, _ ->
                var newUrl = input.text.toString().trim()
                if (newUrl.isNotEmpty()) {
                    if (!newUrl.startsWith("http://") && !newUrl.startsWith("https://")) {
                        newUrl = "http://$newUrl"
                    }
                    if (!newUrl.substringAfter("://").contains(":")) {
                        newUrl = "$newUrl:$DEFAULT_PORT"
                    }
                    prefs.edit().putString(KEY_SERVER_URL, newUrl).apply()
                    webView.loadUrl(newUrl)
                }
            }
            .setNegativeButton("Batal", null)
            .show()
    }

    private fun getDeviceIpAddress(): String? {
        try {
            val interfaces = NetworkInterface.getNetworkInterfaces()
            while (interfaces.hasMoreElements()) {
                val networkInterface = interfaces.nextElement()
                if (networkInterface.isLoopback || !networkInterface.isUp) continue
                val addresses = networkInterface.inetAddresses
                while (addresses.hasMoreElements()) {
                    val address = addresses.nextElement()
                    if (!address.isLoopbackAddress && address is java.net.Inet4Address) {
                        return address.hostAddress
                    }
                }
            }
        } catch (_: Exception) {}
        return null
    }

    private fun isEmulator(): Boolean {
        return (Build.FINGERPRINT.startsWith("generic")
                || Build.FINGERPRINT.startsWith("unknown")
                || Build.MODEL.contains("google_sdk")
                || Build.MODEL.contains("Emulator")
                || Build.MODEL.contains("Android SDK built for x86")
                || Build.MANUFACTURER.contains("Genymotion")
                || (Build.BRAND.startsWith("generic") && Build.DEVICE.startsWith("generic"))
                || "google_sdk" == Build.PRODUCT)
    }

    private fun hideSystemBars() {
        try {
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.R) {
                window.insetsController?.let { controller ->
                    controller.hide(WindowInsets.Type.statusBars() or WindowInsets.Type.navigationBars())
                    controller.systemBarsBehavior = WindowInsetsController.BEHAVIOR_SHOW_TRANSIENT_BARS_BY_SWIPE
                }
            } else {
                @Suppress("DEPRECATION")
                window.decorView.systemUiVisibility = (
                    View.SYSTEM_UI_FLAG_FULLSCREEN
                    or View.SYSTEM_UI_FLAG_HIDE_NAVIGATION
                    or View.SYSTEM_UI_FLAG_IMMERSIVE_STICKY
                    or View.SYSTEM_UI_FLAG_LAYOUT_FULLSCREEN
                    or View.SYSTEM_UI_FLAG_LAYOUT_HIDE_NAVIGATION
                    or View.SYSTEM_UI_FLAG_LAYOUT_STABLE
                )
            }
        } catch (_: Throwable) {}
    }

    override fun onResume() {
        super.onResume()
        hideSystemBars()
    }

    override fun onRequestPermissionsResult(
        requestCode: Int,
        permissions: Array<out String>,
        grantResults: IntArray
    ) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults)
        if (requestCode == PERMISSION_REQUEST_CAMERA) {
            val granted = grantResults.isNotEmpty() && grantResults[0] == PackageManager.PERMISSION_GRANTED
            pendingPermissionRequest?.let { req ->
                runOnUiThread {
                    if (granted) req.grant(req.resources) else req.deny()
                }
                pendingPermissionRequest = null
            }
        }
    }

    inner class PosBridgeInterface {

        @JavascriptInterface
        fun connectToServer(rawUrl: String) {
            runOnUiThread {
                var url = rawUrl.trim()
                if (url.isEmpty()) return@runOnUiThread
                if (!url.startsWith("http://") && !url.startsWith("https://")) {
                    url = "http://$url"
                }
                if (!url.substringAfter("://").contains(":")) {
                    url = "$url:$DEFAULT_PORT"
                }
                prefs.edit().putString(KEY_SERVER_URL, url).apply()
                webView.loadUrl(url)
            }
        }

        @JavascriptInterface
        fun scanSubnet() {
            lifecycleScope.launch(Dispatchers.IO) {
                val localIp = getDeviceIpAddress()
                if (localIp == null) {
                    withContext(Dispatchers.Main) {
                        webView.evaluateJavascript("onScanFinished(false);", null)
                    }
                    return@launch
                }

                val subnet = localIp.substringBeforeLast(".")
                var foundIp: String? = null

                // Pindai host 1..254 secara konkuren
                val jobs = (1..254).map { i ->
                    async(Dispatchers.IO) {
                        if (foundIp != null) return@async null
                        val target = "$subnet.$i"
                        if (target == localIp) return@async null
                        if (isPortOpen(target, DEFAULT_PORT, 220)) {
                            // Cek respons HTTP cepat
                            if (verifyHttpKasir(target, DEFAULT_PORT)) {
                                foundIp = "http://$target:$DEFAULT_PORT"
                            }
                        }
                        null
                    }
                }

                jobs.awaitAll()

                withContext(Dispatchers.Main) {
                    if (foundIp != null) {
                        webView.evaluateJavascript("onServerFound('$foundIp');", null)
                    } else {
                        webView.evaluateJavascript("onScanFinished(false);", null)
                    }
                }
            }
        }

        private fun isPortOpen(host: String, port: Int, timeoutMs: Int): Boolean {
            return try {
                Socket().use { socket ->
                    socket.connect(InetSocketAddress(host, port), timeoutMs)
                    true
                }
            } catch (_: Exception) {
                false
            }
        }

        private fun verifyHttpKasir(host: String, port: Int): Boolean {
            return try {
                val url = URL("http://$host:$port/")
                val conn = url.openConnection() as HttpURLConnection
                conn.connectTimeout = 300
                conn.readTimeout = 400
                conn.instanceFollowRedirects = true
                val code = conn.responseCode
                conn.disconnect()
                code in 200..499
            } catch (_: Exception) {
                false
            }
        }
    }
}
