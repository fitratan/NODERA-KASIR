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

class MainActivity : AppCompatActivity() {

    private lateinit var webView: WebView
    private lateinit var prefs: SharedPreferences

    companion object {
        private const val PREFS_NAME = "NoderaPosPrefs"
        private const val KEY_SERVER_URL = "server_url"
        private const val DEFAULT_URL = "http://10.0.2.2:8088" // Default emulator; di HP fisik: http://192.168.x.x:8088
        private const val PERMISSION_REQUEST_CAMERA = 101
    }

    @SuppressLint("SetJavaScriptEnabled")
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)

        prefs = getSharedPreferences(PREFS_NAME, Context.MODE_PRIVATE)

        // Runtime permission check untuk Kamera (Barcode Scanner)
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.CAMERA) != PackageManager.PERMISSION_GRANTED) {
            ActivityCompat.requestPermissions(this, arrayOf(Manifest.permission.CAMERA), PERMISSION_REQUEST_CAMERA)
        }

        // 1. Orientasi dinamis (Portrait & Landscape sesuai sensor perangkat)
        requestedOrientation = ActivityInfo.SCREEN_ORIENTATION_UNSPECIFIED

        // 2. Mode Immersive Fullscreen (Menyembunyikan status bar & navigasi)
        hideSystemBars()

        // 3. Setup WebView
        webView = WebView(this)
        setContentView(webView)

        val settings: WebSettings = webView.settings
        settings.javaScriptEnabled = true
        settings.domStorageEnabled = true
        settings.databaseEnabled = true
        settings.loadWithOverviewMode = true
        settings.useWideViewPort = true
        settings.allowFileAccess = true
        settings.allowContentAccess = true
        settings.mediaPlaybackRequiresUserGesture = false

        webView.webViewClient = object : WebViewClient() {
            override fun onReceivedError(
                view: WebView?,
                request: WebResourceRequest?,
                error: WebResourceError?
            ) {
                super.onReceivedError(view, request, error)
                if (request?.isForMainFrame == true) {
                    showServerConfigDialog("Gagal terhubung ke server kasir. Periksa apakah server di PC/laptop sudah aktif di port 8088.")
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

        // Handle WebChromeClient: Grant camera/audio permission requests from web page
        webView.webChromeClient = object : WebChromeClient() {
            override fun onPermissionRequest(request: PermissionRequest?) {
                request?.let {
                    runOnUiThread {
                        it.grant(it.resources)
                    }
                }
            }
        }

        // Long click untuk ubah IP server toko
        webView.setOnLongClickListener {
            showServerConfigDialog(null)
            true
        }

        // Handle Tombol Back Fisik / Swipe Gesture Android
        onBackPressedDispatcher.addCallback(this, object : OnBackPressedCallback(true) {
            override fun handleOnBackPressed() {
                if (webView.canGoBack()) {
                    webView.goBack()
                } else {
                    AlertDialog.Builder(this@MainActivity)
                        .setTitle("Keluar Aplikasi")
                        .setMessage("Apakah Anda yakin ingin keluar dari Kasir Nodera POS?")
                        .setPositiveButton("Ya") { _, _ -> finish() }
                        .setNegativeButton("Tidak", null)
                        .show()
                }
            }
        })

        loadCurrentServerUrl()
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
        } catch (e: Exception) {
            // Jika aplikasi RawBT printer belum terpasang di Android kasir
            if (url.contains("ru.a402d.rawbtprinter")) {
                try {
                    startActivity(Intent(Intent.ACTION_VIEW, Uri.parse("market://details?id=ru.a402d.rawbtprinter")))
                    return true
                } catch (ex: Exception) {
                    startActivity(Intent(Intent.ACTION_VIEW, Uri.parse("https://play.google.com/store/apps/details?id=ru.a402d.rawbtprinter")))
                    return true
                }
            }
        }
        return true
    }

    private fun loadCurrentServerUrl() {
        val currentUrl = prefs.getString(KEY_SERVER_URL, DEFAULT_URL) ?: DEFAULT_URL
        webView.loadUrl(currentUrl)
    }

    private fun showServerConfigDialog(message: String?) {
        val currentUrl = prefs.getString(KEY_SERVER_URL, DEFAULT_URL) ?: DEFAULT_URL
        val input = EditText(this)
        input.hint = "http://192.168.1.100:8088"
        input.setText(currentUrl)

        AlertDialog.Builder(this)
            .setTitle("Konfigurasi Server Kasir")
            .setMessage(message ?: "Masukkan alamat IP server kasir Nodera (PC/Laptop):")
            .setView(input)
            .setPositiveButton("Hubungkan") { _, _ ->
                var newUrl = input.text.toString().trim()
                if (!newUrl.startsWith("http://") && !newUrl.startsWith("https://")) {
                    newUrl = "http://$newUrl"
                }
                prefs.edit().putString(KEY_SERVER_URL, newUrl).apply()
                webView.loadUrl(newUrl)
            }
            .setNegativeButton("Batal", null)
            .show()
    }

    private fun hideSystemBars() {
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
    }

    override fun onResume() {
        super.onResume()
        hideSystemBars()
    }
}
