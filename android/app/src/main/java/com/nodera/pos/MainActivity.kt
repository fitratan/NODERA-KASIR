package com.nodera.pos

import android.annotation.SuppressLint
import android.content.Context
import android.content.Intent
import android.content.pm.ActivityInfo
import android.content.pm.PackageManager
import android.graphics.Bitmap
import android.graphics.Color
import android.graphics.Typeface
import android.graphics.drawable.GradientDrawable
import android.content.res.ColorStateList
import android.view.animation.AccelerateDecelerateInterpolator
import android.widget.ImageView
import android.net.Uri
import android.os.Build
import android.os.Bundle
import android.provider.Settings
import android.view.Gravity
import android.view.View
import android.view.ViewGroup
import android.view.WindowInsets
import android.view.WindowInsetsController
import android.webkit.ConsoleMessage
import android.webkit.CookieManager
import android.webkit.JavascriptInterface
import android.webkit.PermissionRequest
import android.webkit.ValueCallback
import android.webkit.WebChromeClient
import android.webkit.WebResourceError
import android.webkit.WebResourceRequest
import android.webkit.WebSettings
import android.webkit.WebView
import android.webkit.WebViewClient
import android.widget.FrameLayout
import android.widget.LinearLayout
import android.widget.ProgressBar
import android.widget.TextView
import androidx.activity.OnBackPressedCallback
import androidx.activity.result.contract.ActivityResultContracts
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import androidx.core.view.WindowCompat
import androidx.core.view.WindowInsetsCompat
import androidx.lifecycle.lifecycleScope
import com.nodera.pos.server.PhpServerManager
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext

class MainActivity : AppCompatActivity() {

    private lateinit var rootContainer: FrameLayout
    private lateinit var webView: WebView
    private lateinit var splashView: FrameLayout
    private lateinit var splashStatusText: TextView
    private lateinit var serverManager: PhpServerManager

    private var pendingPermissionRequest: PermissionRequest? = null
    private var fileUploadCallback: ValueCallback<Array<Uri>>? = null

    private val cameraPermissionLauncher = registerForActivityResult(
        ActivityResultContracts.RequestPermission()
    ) { isGranted ->
        pendingPermissionRequest?.let { req ->
            runOnUiThread {
                if (isGranted) {
                    req.grant(req.resources)
                } else {
                    req.deny()
                }
            }
            pendingPermissionRequest = null
        }
    }

    private val filePickerLauncher = registerForActivityResult(
        ActivityResultContracts.StartActivityForResult()
    ) { result ->
        if (fileUploadCallback == null) return@registerForActivityResult
        val results = WebChromeClient.FileChooserParams.parseResult(result.resultCode, result.data)
        fileUploadCallback?.onReceiveValue(results)
        fileUploadCallback = null
    }

    @SuppressLint("SetJavaScriptEnabled")
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        try {
            serverManager = PhpServerManager(this)

            // Dynamic Sensor Orientation (Landscape/Portrait support)
            try {
                requestedOrientation = ActivityInfo.SCREEN_ORIENTATION_UNSPECIFIED
            } catch (_: Throwable) {}

            setupSystemBars()

            // Root layout containing WebView + Splash loading overlay
            rootContainer = FrameLayout(this).apply {
                layoutParams = ViewGroup.LayoutParams(
                    ViewGroup.LayoutParams.MATCH_PARENT,
                    ViewGroup.LayoutParams.MATCH_PARENT
                )
                setBackgroundColor(Color.parseColor("#08101E"))
            }

            webView = WebView(this).apply {
                layoutParams = ViewGroup.LayoutParams(
                    ViewGroup.LayoutParams.MATCH_PARENT,
                    ViewGroup.LayoutParams.MATCH_PARENT
                )
                visibility = View.INVISIBLE
                setBackgroundColor(Color.parseColor("#08101E"))
            }

            splashView = createSplashView()

            rootContainer.addView(webView)
            rootContainer.addView(splashView)
            setContentView(rootContainer)

            setupWebViewSettings()
            setupWebViewClients()

            // Handle back button / gesture
            onBackPressedDispatcher.addCallback(this, object : OnBackPressedCallback(true) {
                override fun handleOnBackPressed() {
                    if (this@MainActivity::webView.isInitialized && webView.canGoBack()) {
                        webView.goBack()
                    } else {
                        if (!isFinishing && !isDestroyed) {
                            AlertDialog.Builder(this@MainActivity)
                                .setTitle("Keluar Aplikasi")
                                .setMessage("Apakah Anda yakin ingin menutup NODERA KASIR POS?")
                                .setPositiveButton("Ya") { _, _ -> finish() }
                                .setNegativeButton("Tidak", null)
                                .show()
                        }
                    }
                }
            })

            startStandaloneServer()

        } catch (t: Throwable) {
            android.util.Log.e("NoderaPOS", "Fatal onCreate error", t)
        }
    }

    private fun dp(value: Int): Int {
        return (value * resources.displayMetrics.density).toInt()
    }

    private fun createSplashView(): FrameLayout {
        val root = FrameLayout(this).apply {
            layoutParams = ViewGroup.LayoutParams(
                ViewGroup.LayoutParams.MATCH_PARENT,
                ViewGroup.LayoutParams.MATCH_PARENT
            )
            setBackgroundColor(Color.parseColor("#08101E"))
        }

        // Center Content Box
        val centerBox = LinearLayout(this).apply {
            orientation = LinearLayout.VERTICAL
            gravity = Gravity.CENTER_HORIZONTAL
            layoutParams = FrameLayout.LayoutParams(
                ViewGroup.LayoutParams.MATCH_PARENT,
                ViewGroup.LayoutParams.WRAP_CONTENT,
                Gravity.CENTER
            ).apply {
                setPadding(dp(24), 0, dp(24), dp(36))
            }
        }

        // Logo (Clean standalone without background card box)
        val logoImage = ImageView(this).apply {
            val imgSize = dp(76)
            layoutParams = LinearLayout.LayoutParams(imgSize, imgSize).apply {
                gravity = Gravity.CENTER_HORIZONTAL
            }
            setImageResource(R.drawable.nodera_logo)
            scaleType = ImageView.ScaleType.FIT_CENTER
        }

        // Subtle gentle breathing animation on logo
        logoImage.animate()
            .scaleX(1.05f)
            .scaleY(1.05f)
            .setDuration(1200)
            .setInterpolator(AccelerateDecelerateInterpolator())
            .withEndAction {
                logoImage.animate()
                    .scaleX(0.98f)
                    .scaleY(0.98f)
                    .setDuration(1200)
                    .setInterpolator(AccelerateDecelerateInterpolator())
                    .start()
            }
            .start()

        centerBox.addView(logoImage)

        // Title
        val titleView = TextView(this).apply {
            text = "NODERA POS"
            textSize = 21f
            setTextColor(Color.WHITE)
            typeface = Typeface.create("sans-serif-medium", Typeface.BOLD)
            letterSpacing = 0.08f
            gravity = Gravity.CENTER
            layoutParams = LinearLayout.LayoutParams(
                ViewGroup.LayoutParams.WRAP_CONTENT,
                ViewGroup.LayoutParams.WRAP_CONTENT
            ).apply {
                topMargin = dp(20)
                gravity = Gravity.CENTER_HORIZONTAL
            }
        }
        centerBox.addView(titleView)

        // Subtitle
        val subtitleView = TextView(this).apply {
            text = "Smart POS & Retail Intelligence"
            textSize = 12.5f
            setTextColor(Color.parseColor("#94A3B8"))
            letterSpacing = 0.02f
            gravity = Gravity.CENTER
            layoutParams = LinearLayout.LayoutParams(
                ViewGroup.LayoutParams.WRAP_CONTENT,
                ViewGroup.LayoutParams.WRAP_CONTENT
            ).apply {
                topMargin = dp(6)
                gravity = Gravity.CENTER_HORIZONTAL
            }
        }
        centerBox.addView(subtitleView)

        // Slim Progress Bar
        val progressBar = ProgressBar(this, null, android.R.attr.progressBarStyleHorizontal).apply {
            isIndeterminate = true
            indeterminateTintList = ColorStateList.valueOf(Color.parseColor("#38BDF8"))
            layoutParams = LinearLayout.LayoutParams(dp(150), dp(3)).apply {
                topMargin = dp(28)
                gravity = Gravity.CENTER_HORIZONTAL
            }
        }
        centerBox.addView(progressBar)

        // Status Text
        splashStatusText = TextView(this).apply {
            text = "Menyiapkan engine kasir..."
            textSize = 11.5f
            setTextColor(Color.parseColor("#64748B"))
            letterSpacing = 0.02f
            gravity = Gravity.CENTER
            layoutParams = LinearLayout.LayoutParams(
                ViewGroup.LayoutParams.WRAP_CONTENT,
                ViewGroup.LayoutParams.WRAP_CONTENT
            ).apply {
                topMargin = dp(12)
                gravity = Gravity.CENTER_HORIZONTAL
            }
        }
        centerBox.addView(splashStatusText)

        root.addView(centerBox)

        // Bottom Footer
        val footerView = LinearLayout(this).apply {
            orientation = LinearLayout.VERTICAL
            gravity = Gravity.CENTER_HORIZONTAL
            layoutParams = FrameLayout.LayoutParams(
                ViewGroup.LayoutParams.MATCH_PARENT,
                ViewGroup.LayoutParams.WRAP_CONTENT,
                Gravity.BOTTOM
            ).apply {
                bottomMargin = dp(24)
            }
        }

        val footerBrand = TextView(this).apply {
            text = "NODERA ECOSYSTEM"
            textSize = 9.5f
            setTextColor(Color.parseColor("#475569"))
            typeface = Typeface.DEFAULT_BOLD
            letterSpacing = 0.15f
            gravity = Gravity.CENTER
        }
        footerView.addView(footerBrand)

        val footerVer = TextView(this).apply {
            text = "v1.0.4 • Standalone Edition"
            textSize = 9f
            setTextColor(Color.parseColor("#334155"))
            gravity = Gravity.CENTER
            setPadding(0, dp(2), 0, 0)
        }
        footerView.addView(footerVer)

        root.addView(footerView)

        return root
    }

    private fun startStandaloneServer() {
        val hwid = try {
            Settings.Secure.getString(contentResolver, Settings.Secure.ANDROID_ID) ?: "pos-device-standalone"
        } catch (_: Exception) {
            "pos-device-standalone"
        }

        lifecycleScope.launch(Dispatchers.IO) {
            try {
                withContext(Dispatchers.Main) {
                    splashStatusText.text = "Mengekstrak engine kasir..."
                }

                serverManager.prepareEnvironment(hwid).getOrThrow()

                withContext(Dispatchers.Main) {
                    splashStatusText.text = "Menjalankan database & web server..."
                }

                val targetPort = serverManager.findAvailablePort(PhpServerManager.DEFAULT_PORT)
                val port = serverManager.startServer(hwid, targetPort).getOrThrow()

                withContext(Dispatchers.Main) {
                    splashStatusText.text = "Membuka kasir..."
                    val url = "http://127.0.0.1:$port/"
                    webView.loadUrl(url)
                }
            } catch (e: Throwable) {
                android.util.Log.e("NoderaPOS", "Failed to start standalone server", e)
                withContext(Dispatchers.Main) {
                    splashStatusText.text = "Gagal memulai: ${e.message}"
                    if (!isFinishing && !isDestroyed) {
                        AlertDialog.Builder(this@MainActivity)
                            .setTitle("Kesalahan Inisialisasi POS")
                            .setMessage("Gagal menjalankan engine lokal POS:\n\n${e.message}\n\nSilakan coba lagi.")
                            .setPositiveButton("Coba Lagi") { _, _ ->
                                startStandaloneServer()
                            }
                            .setNegativeButton("Tutup") { _, _ ->
                                finish()
                            }
                            .setCancelable(false)
                            .show()
                    }
                }
            }
        }
    }

    @SuppressLint("SetJavaScriptEnabled")
    private fun setupWebViewSettings() {
        webView.settings.apply {
            javaScriptEnabled = true
            domStorageEnabled = true
            databaseEnabled = true
            useWideViewPort = true
            loadWithOverviewMode = true
            displayZoomControls = false
            builtInZoomControls = false
            setSupportZoom(false)
            cacheMode = WebSettings.LOAD_NO_CACHE
            allowFileAccess = true
            allowContentAccess = true
            mediaPlaybackRequiresUserGesture = false
            mixedContentMode = WebSettings.MIXED_CONTENT_ALWAYS_ALLOW
            userAgentString = "${userAgentString} NoderaKasirStandalone/2.5.0"
        }

        CookieManager.getInstance().apply {
            setAcceptCookie(true)
            setAcceptThirdPartyCookies(webView, true)
        }

        webView.addJavascriptInterface(AndroidBridge(), "AndroidBridge")
    }

    inner class AndroidBridge {
        @JavascriptInterface
        fun setOrientation(orientation: String) {
            runOnUiThread {
                when (orientation.lowercase()) {
                    "landscape", "sensor_landscape" -> {
                        requestedOrientation = ActivityInfo.SCREEN_ORIENTATION_SENSOR_LANDSCAPE
                    }
                    "portrait", "sensor_portrait" -> {
                        requestedOrientation = ActivityInfo.SCREEN_ORIENTATION_PORTRAIT
                    }
                    "auto", "sensor", "unspecified" -> {
                        requestedOrientation = ActivityInfo.SCREEN_ORIENTATION_UNSPECIFIED
                    }
                }
            }
        }

        @JavascriptInterface
        fun isAndroidApp(): Boolean = true
    }

    private fun setupWebViewClients() {
        webView.webViewClient = object : WebViewClient() {
            override fun onPageStarted(view: WebView?, url: String?, favicon: Bitmap?) {
                super.onPageStarted(view, url, favicon)
            }

            override fun onPageFinished(view: WebView?, url: String?) {
                super.onPageFinished(view, url)
                // Smooth fade transition from splash to webview
                if (splashView.visibility == View.VISIBLE) {
                    webView.alpha = 0f
                    webView.visibility = View.VISIBLE
                    webView.animate()
                        .alpha(1f)
                        .setDuration(350)
                        .start()

                    splashView.animate()
                        .alpha(0f)
                        .setDuration(350)
                        .withEndAction {
                            splashView.visibility = View.GONE
                        }
                        .start()
                }
            }

            override fun onReceivedError(
                view: WebView?,
                request: WebResourceRequest?,
                error: WebResourceError?
            ) {
                super.onReceivedError(view, request, error)
                if (request?.isForMainFrame == true) {
                    android.util.Log.e("NoderaPOS", "MainFrame Error: ${error?.description}")
                }
            }

            override fun shouldOverrideUrlLoading(view: WebView?, request: WebResourceRequest?): Boolean {
                val url = request?.url?.toString() ?: return false

                // RawBT Intent URL Schemes
                if (url.startsWith("intent:")) {
                    return try {
                        val intent = Intent.parseUri(url, Intent.URI_INTENT_SCHEME)
                        if (intent != null) {
                            intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
                            val resolveInfo = packageManager.resolveActivity(intent, PackageManager.MATCH_DEFAULT_ONLY)
                            if (resolveInfo != null) {
                                startActivity(intent)
                                return true
                            }
                            val fallbackUrl = intent.getStringExtra("browser_fallback_url")
                            if (!fallbackUrl.isNullOrEmpty()) {
                                val fallbackIntent = Intent(Intent.ACTION_VIEW, Uri.parse(fallbackUrl))
                                fallbackIntent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
                                startActivity(fallbackIntent)
                                return true
                            }
                            val pkg = intent.`package`
                            if (!pkg.isNullOrEmpty()) {
                                val marketIntent = Intent(Intent.ACTION_VIEW, Uri.parse("market://details?id=$pkg"))
                                marketIntent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
                                startActivity(marketIntent)
                                return true
                            }
                        }
                        true
                    } catch (_: Exception) {
                        true
                    }
                }

                // RawBT thermal printing direct scheme
                if (url.startsWith("rawbt:")) {
                    return try {
                        val intent = Intent(Intent.ACTION_VIEW, Uri.parse(url))
                        intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
                        startActivity(intent)
                        true
                    } catch (_: Exception) {
                        try {
                            val marketIntent = Intent(Intent.ACTION_VIEW, Uri.parse("market://details?id=ru.a402d.rawbtprinter"))
                            marketIntent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK)
                            startActivity(marketIntent)
                        } catch (_: Exception) {}
                        true
                    }
                }

                // WhatsApp integration
                if (url.startsWith("whatsapp:") || url.startsWith("https://wa.me/") || url.startsWith("https://api.whatsapp.com/")) {
                    return try {
                        val intent = Intent(Intent.ACTION_VIEW, Uri.parse(url))
                        startActivity(intent)
                        true
                    } catch (_: Exception) {
                        false
                    }
                }

                // External links (tel:, mailto:)
                if (url.startsWith("tel:") || url.startsWith("mailto:")) {
                    return try {
                        val intent = Intent(Intent.ACTION_VIEW, Uri.parse(url))
                        startActivity(intent)
                        true
                    } catch (_: Exception) {
                        false
                    }
                }

                return false
            }
        }

        webView.webChromeClient = object : WebChromeClient() {
            override fun onPermissionRequest(request: PermissionRequest?) {
                if (request == null) return
                val requested = request.resources
                val needsCamera = requested.any { it == PermissionRequest.RESOURCE_VIDEO_CAPTURE }

                if (needsCamera) {
                    if (ContextCompat.checkSelfPermission(this@MainActivity, android.Manifest.permission.CAMERA)
                        == PackageManager.PERMISSION_GRANTED
                    ) {
                        request.grant(requested)
                    } else {
                        pendingPermissionRequest = request
                        cameraPermissionLauncher.launch(android.Manifest.permission.CAMERA)
                    }
                } else {
                    request.grant(requested)
                }
            }

            override fun onShowFileChooser(
                mWebView: WebView?,
                filePathCallback: ValueCallback<Array<Uri>>?,
                fileChooserParams: FileChooserParams?
            ): Boolean {
                fileUploadCallback?.onReceiveValue(null)
                fileUploadCallback = filePathCallback

                val intent = fileChooserParams?.createIntent() ?: Intent(Intent.ACTION_GET_CONTENT).apply {
                    type = "*/*"
                    addCategory(Intent.CATEGORY_OPENABLE)
                }

                return try {
                    filePickerLauncher.launch(intent)
                    true
                } catch (e: Exception) {
                    fileUploadCallback = null
                    false
                }
            }

            override fun onConsoleMessage(message: ConsoleMessage?): Boolean {
                message?.let {
                    android.util.Log.d("NoderaPOS-Web", "[${it.messageLevel()}] ${it.message()} -- line ${it.lineNumber()}")
                }
                return true
            }
        }
    }

    private fun setupSystemBars() {
        try {
            WindowCompat.setDecorFitsSystemWindows(window, true)
            val insetsController = WindowCompat.getInsetsController(window, window.decorView)
            insetsController.show(WindowInsetsCompat.Type.statusBars())
            insetsController.isAppearanceLightStatusBars = false

            window.statusBarColor = Color.parseColor("#08101E")
            window.navigationBarColor = Color.parseColor("#08101E")
        } catch (_: Throwable) {}
    }

    override fun onConfigurationChanged(newConfig: android.content.res.Configuration) {
        super.onConfigurationChanged(newConfig)
        setupSystemBars()
    }

    override fun onResume() {
        super.onResume()
        setupSystemBars()
    }

    override fun onDestroy() {
        super.onDestroy()
        try {
            serverManager.stopServer()
        } catch (_: Exception) {}
    }
}
