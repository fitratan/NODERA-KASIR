package main

import (
	"encoding/json"
	"fmt"
	"os"
	"path/filepath"
	"syscall"
	"time"

	webview2 "github.com/jchv/go-webview2"
)

var (
	procMessageBoxWApp = user32DLL.NewProc("MessageBoxW")
)

func showAppDialog(title, message string, iconType uint) int {
	tPtr, _ := syscall.UTF16PtrFromString(title)
	mPtr, _ := syscall.UTF16PtrFromString(message)
	ret, _, _ := procMessageBoxWApp.Call(0, uintptr(unsafePointer(mPtr)), uintptr(unsafePointer(tPtr)), uintptr(iconType))
	return int(ret)
}

func RunDesktopApp(baseDir string) {
	// 1. Initialize Server Manager on port 8088
	sm := NewServerManager(baseDir, 8088)
	lanIp := GetLocalLANIP()

	// 2. Start internal PHP server
	if err := sm.Start(); err != nil {
		showAppDialog("Gagal Memulai Server", fmt.Sprintf("Gagal menjalankan engine PHP internal: %v", err), 0x00000010)
		return
	}
	defer sm.Stop()

	// 3. Setup Persistent WebView2 Data Path in %APPDATA%\NoderaKasir
	appData := os.Getenv("APPDATA")
	if appData == "" {
		appData = baseDir
	}
	dataPath := filepath.Join(appData, "NoderaKasir", "webview_cache")
	_ = os.MkdirAll(dataPath, 0755)

	// 4. Create Native WebView2 Window
	w := webview2.NewWithOptions(webview2.WebViewOptions{
		Debug:     false,
		DataPath:  dataPath,
		AutoFocus: true,
		WindowOptions: webview2.WindowOptions{
			Title:  "NODERA KASIR POS — Point of Sale & Inventory System",
			Width:  1280,
			Height: 820,
			IconId: 1,
			Center: true,
		},
	})

	// Fallback if WebView2 runtime is missing on old Windows machines
	if w == nil {
		appUrl := fmt.Sprintf("http://127.0.0.1:%d", sm.Port)
		_, _ = OpenAppWindow(appUrl)

		// Keep process alive while monitoring PHP server
		for {
			time.Sleep(2 * time.Second)
			if sm.Cmd != nil && sm.Cmd.ProcessState != nil && sm.Cmd.ProcessState.Exited() {
				break
			}
		}
		return
	}
	defer w.Destroy()

	// 5. Bind Native Go APIs to JavaScript
	w.Bind("getLanInfo", func() (map[string]interface{}, error) {
		return map[string]interface{}{
			"lan_ip":    lanIp,
			"port":      sm.Port,
			"pos_url":   fmt.Sprintf("http://%s:%d", lanIp, sm.Port),
			"admin_url": fmt.Sprintf("http://%s:%d/admin", lanIp, sm.Port),
		}, nil
	})

	w.Bind("openExternal", func(targetUrl string) (bool, error) {
		_ = OpenBrowser(targetUrl)
		return true, nil
	})

	w.Bind("openAdmin", func() (bool, error) {
		w.Navigate(fmt.Sprintf("http://127.0.0.1:%d/admin", sm.Port))
		return true, nil
	})

	w.Bind("openPos", func() (bool, error) {
		w.Navigate(fmt.Sprintf("http://127.0.0.1:%d", sm.Port))
		return true, nil
	})

	// 6. Inject Navigation & Bridge Scripts
	w.Init(getInjectedDesktopScript(sm.Port, lanIp))

	// 7. Navigate directly to Kasir POS home
	w.Navigate(fmt.Sprintf("http://127.0.0.1:%d", sm.Port))

	// 8. Run Native Win32 Message Loop
	w.Run()
}

func getInjectedDesktopScript(port int, lanIp string) string {
	lanIpJson, _ := json.Marshal(lanIp)
	return fmt.Sprintf(`
(function() {
  window.noderaKasir = {
    lanIp: %s,
    port: %d,
    openExternal: function(url) {
      if (window.openExternal) window.openExternal(url);
      else window.open(url, '_blank');
    },
    openAdmin: function() {
      if (window.openAdmin) window.openAdmin();
      else window.location.href = './admin';
    },
    openPos: function() {
      if (window.openPos) window.openPos();
      else window.location.href = './';
    }
  };

  // Keyboard Shortcuts: F5 / Ctrl+R (reload), F1 (POS), F2 (Admin)
  window.addEventListener('keydown', function(e) {
    if (e.key === 'F5' || (e.ctrlKey && e.key.toLowerCase() === 'r')) {
      e.preventDefault();
      window.location.reload();
    }
    if (e.key === 'F1') {
      e.preventDefault();
      window.location.href = '/';
    }
    if (e.key === 'F2') {
      e.preventDefault();
      window.location.href = '/admin';
    }
  });
})();
`, string(lanIpJson), port)
}
