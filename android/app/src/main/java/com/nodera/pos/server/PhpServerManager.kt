package com.nodera.pos.server

import android.content.Context
import android.util.Log
import com.nodera.pos.BuildConfig
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.withContext
import java.io.File
import java.io.FileInputStream
import java.io.FileOutputStream
import java.net.HttpURLConnection
import java.net.InetSocketAddress
import java.net.ServerSocket
import java.net.Socket
import java.net.URL
import java.util.zip.ZipInputStream

class PhpServerManager(private val context: Context) {

    companion object {
        private const val TAG = "PhpServerManager"
        const val DEFAULT_PORT = 8088
    }

    private var serverProcess: Process? = null

    val phpBinary: File
        get() {
            val nativeBin = File(context.applicationInfo.nativeLibraryDir, "libphp.so")
            if (nativeBin.exists() && nativeBin.length() > 0) return nativeBin

            val filesBin = File(File(context.filesDir, "bin"), "php")
            if (filesBin.exists() && filesBin.length() > 0) return filesBin

            return nativeBin
        }

    val kasirDir: File = File(context.filesDir, "kasir")
    val logFile: File = File(context.filesDir, "php_server.log")
    private val versionFile: File = File(context.filesDir, ".installed_version")

    fun isServerListening(port: Int = DEFAULT_PORT): Boolean {
        return try {
            Socket().use { socket ->
                socket.connect(InetSocketAddress("127.0.0.1", port), 250)
                true
            }
        } catch (_: Exception) {
            false
        }
    }

    fun isHttpReady(port: Int = DEFAULT_PORT): Boolean {
        return try {
            val url = URL("http://127.0.0.1:$port/index.php")
            val conn = url.openConnection() as HttpURLConnection
            conn.connectTimeout = 400
            conn.readTimeout = 800
            conn.instanceFollowRedirects = false
            val code = conn.responseCode
            conn.disconnect()
            code in 200..399
        } catch (_: Exception) {
            false
        }
    }

    fun findAvailablePort(startPort: Int = DEFAULT_PORT): Int {
        for (port in startPort until startPort + 50) {
            try {
                ServerSocket(port).use {
                    return port
                }
            } catch (_: Exception) {
                // Port occupied, test next
            }
        }
        return startPort
    }

    suspend fun prepareEnvironment(hwid: String): Result<Unit> = withContext(Dispatchers.IO) {
        runCatching {
            val installedVersion = if (versionFile.exists()) {
                versionFile.readText().trim()
            } else ""

            val currentVersion = BuildConfig.VERSION_CODE.toString()
            val nativeBin = File(context.applicationInfo.nativeLibraryDir, "libphp.so")
            val filesBin = File(File(context.filesDir, "bin"), "php")

            if (installedVersion != currentVersion && filesBin.exists()) {
                filesBin.delete()
            }

            // Fallback manual extract if OS did not unpack libphp.so into nativeLibraryDir
            if (!nativeBin.exists() && !filesBin.exists()) {
                Log.w(TAG, "libphp.so not found in nativeLibraryDir. Extracting from APK...")
                try {
                    filesBin.parentFile?.mkdirs()
                    ZipInputStream(FileInputStream(context.packageCodePath)).use { zipIn ->
                        var entry = zipIn.nextEntry
                        while (entry != null) {
                            if (entry.name == "lib/arm64-v8a/libphp.so") {
                                FileOutputStream(filesBin).use { out ->
                                    zipIn.copyTo(out)
                                }
                                ensureExecutable(filesBin)
                                Log.i(TAG, "Extracted libphp.so to ${filesBin.absolutePath} (${filesBin.length()} bytes)")
                                break
                            }
                            zipIn.closeEntry()
                            entry = zipIn.nextEntry
                        }
                    }
                } catch (e: Exception) {
                    Log.e(TAG, "Failed to extract libphp.so from APK: ${e.message}")
                }
            } else if (nativeBin.exists()) {
                ensureExecutable(nativeBin)
            }

            val needsWebExtract = installedVersion != currentVersion ||
                    !File(kasirDir, "public/index.php").exists() ||
                    !File(kasirDir, "app/config/database.php").exists()

            if (needsWebExtract) {
                Log.i(TAG, "Extracting kasir.zip web assets into ${kasirDir.absolutePath}...")
                if (!kasirDir.exists()) {
                    kasirDir.mkdirs()
                }

                context.assets.open("kasir.zip").use { assetIn ->
                    ZipInputStream(assetIn).use { zipIn ->
                        var entry = zipIn.nextEntry
                        while (entry != null) {
                            val entryName = entry.name
                            if (entryName.isNotEmpty()) {
                                val targetFile = File(kasirDir, entryName)
                                if (!targetFile.canonicalPath.startsWith(kasirDir.canonicalPath)) {
                                    zipIn.closeEntry()
                                    entry = zipIn.nextEntry
                                    continue
                                }
                                if (entry.isDirectory) {
                                    targetFile.mkdirs()
                                } else {
                                    targetFile.parentFile?.mkdirs()
                                    // PRESERVE customer database and existing user data!
                                    val isUserData = (entryName.startsWith("app/database/") &&
                                            (entryName.endsWith(".sqlite") || entryName.endsWith(".db"))) &&
                                            targetFile.exists()

                                    if (!isUserData) {
                                        FileOutputStream(targetFile).use { out ->
                                            zipIn.copyTo(out)
                                        }
                                    }
                                }
                            }
                            zipIn.closeEntry()
                            entry = zipIn.nextEntry
                        }
                    }
                }

                versionFile.writeText(currentVersion)
                Log.i(TAG, "Kasir web assets extraction complete.")
            }
        }
    }

    private fun ensureExecutable(file: File) {
        file.setReadable(true, false)
        file.setExecutable(true, false)
        try {
            Runtime.getRuntime().exec(arrayOf("chmod", "755", file.absolutePath)).waitFor()
        } catch (e: Exception) {
            Log.w(TAG, "chmod 755 failed: ${e.message}")
        }
    }

    suspend fun startServer(hwid: String, port: Int = DEFAULT_PORT): Result<Int> = withContext(Dispatchers.IO) {
        runCatching {
            if (isHttpReady(port)) {
                Log.i(TAG, "PHP server already running on port $port")
                return@runCatching port
            }

            val binaryToUse = phpBinary
            if (!binaryToUse.exists()) {
                throw IllegalStateException("Binary PHP tidak ditemukan di ${binaryToUse.absolutePath}")
            }
            ensureExecutable(binaryToUse)

            val publicDir = File(kasirDir, "public")
            if (!File(publicDir, "index.php").exists()) {
                throw IllegalStateException("File index.php tidak ditemukan di ${publicDir.absolutePath}")
            }

            val cacheDir = context.cacheDir.absolutePath
            val sessionDir = File(context.filesDir, "sessions")
            if (!sessionDir.exists()) sessionDir.mkdirs()

            val phpIni = File(context.filesDir, "php.ini")
            phpIni.writeText(
                """
                [PHP]
                memory_limit = 256M
                max_execution_time = 0
                max_input_time = -1
                upload_max_filesize = 32M
                post_max_size = 32M
                sys_temp_dir = "$cacheDir"
                upload_tmp_dir = "$cacheDir"
                session.save_path = "${sessionDir.absolutePath}"
                date.timezone = "Asia/Jakarta"
                """.trimIndent()
            )

            if (logFile.exists() && logFile.length() > 500_000) {
                logFile.writeText("")
            }

            val pb = ProcessBuilder(
                binaryToUse.absolutePath,
                "-c", phpIni.absolutePath,
                "-d", "max_execution_time=0",
                "-d", "upload_max_filesize=32M",
                "-d", "post_max_size=32M",
                "-d", "sys_temp_dir=$cacheDir",
                "-d", "upload_tmp_dir=$cacheDir",
                "-d", "session.save_path=${sessionDir.absolutePath}",
                "-S", "127.0.0.1:$port",
                "-t", publicDir.absolutePath
            )
            pb.directory(publicDir)
            pb.redirectErrorStream(true)

            val env = pb.environment()
            val cleanHwid = if (hwid.startsWith("NDR-POS-")) {
                hwid
            } else {
                val hexOnly = hwid.filter { it.isLetterOrDigit() }.uppercase()
                val chunk = hexOnly.take(12).padEnd(12, '0').chunked(4).joinToString("-")
                "NDR-POS-$chunk"
            }
            env["NODERA_POS_HWID"] = cleanHwid
            env["NODERA_DESKTOP_HWID"] = cleanHwid
            env["PHP_CLI_SERVER_WORKERS"] = "4"
            env["TMPDIR"] = cacheDir
            env["TEMP"] = cacheDir
            env["TMP"] = cacheDir
            env["HOME"] = context.filesDir.absolutePath
            val nativeLibDir = context.applicationInfo.nativeLibraryDir
            env["LD_LIBRARY_PATH"] = "$nativeLibDir:/system/lib64:/vendor/lib64"

            pb.redirectOutput(ProcessBuilder.Redirect.appendTo(logFile))

            Log.i(TAG, "Starting standalone PHP server on 127.0.0.1:$port (docroot=${publicDir.absolutePath})...")
            val process = pb.start()
            serverProcess = process

            val startTime = System.currentTimeMillis()
            val timeoutMs = 15000L
            var ready = false

            while (System.currentTimeMillis() - startTime < timeoutMs) {
                if (isHttpReady(port)) {
                    ready = true
                    break
                }
                if (isServerListening(port)) {
                    kotlinx.coroutines.delay(200)
                    if (isHttpReady(port)) {
                        ready = true
                        break
                    }
                }
                if (!process.isAlive) {
                    val logSnippet = if (logFile.exists()) {
                        logFile.readLines().takeLast(30).joinToString("\n")
                    } else "No logs"
                    throw IllegalStateException("PHP process crashed prematurely (exit=${process.exitValue()}):\n$logSnippet")
                }
                kotlinx.coroutines.delay(200)
            }

            if (!ready) {
                process.destroy()
                val logSnippet = if (logFile.exists()) {
                    logFile.readLines().takeLast(30).joinToString("\n")
                } else "No logs"
                throw IllegalStateException("PHP server timeout waiting for HTTP ready on port $port:\n$logSnippet")
            }

            Log.i(TAG, "PHP standalone server successfully started on port $port")
            port
        }
    }

    fun stopServer() {
        try {
            serverProcess?.let {
                if (it.isAlive) {
                    it.destroy()
                }
            }
            serverProcess = null
        } catch (e: Exception) {
            Log.w(TAG, "Error stopping PHP server: ${e.message}")
        }
    }
}
