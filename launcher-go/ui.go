package main

import (
	"fmt"
	"runtime"
	"syscall"
	"unsafe"
)

var (
	user32DLL   = syscall.NewLazyDLL("user32.dll")
	kernel32DLL = syscall.NewLazyDLL("kernel32.dll")
	gdi32DLL    = syscall.NewLazyDLL("gdi32.dll")
	shell32DLL  = syscall.NewLazyDLL("shell32.dll")

	procRegisterClassExW   = user32DLL.NewProc("RegisterClassExW")
	procCreateWindowExW     = user32DLL.NewProc("CreateWindowExW")
	procDefWindowProcW      = user32DLL.NewProc("DefWindowProcW")
	procShowWindow          = user32DLL.NewProc("ShowWindow")
	procUpdateWindow        = user32DLL.NewProc("UpdateWindow")
	procGetMessageW         = user32DLL.NewProc("GetMessageW")
	procTranslateMessage    = user32DLL.NewProc("TranslateMessage")
	procDispatchMessageW    = user32DLL.NewProc("DispatchMessageW")
	procPostQuitMessage     = user32DLL.NewProc("PostQuitMessage")
	procDestroyWindow       = user32DLL.NewProc("DestroyWindow")
	procCreatePopupMenu     = user32DLL.NewProc("CreatePopupMenu")
	procAppendMenuW         = user32DLL.NewProc("AppendMenuW")
	procTrackPopupMenu      = user32DLL.NewProc("TrackPopupMenu")
	procDestroyMenu         = user32DLL.NewProc("DestroyMenu")
	procGetCursorPos        = user32DLL.NewProc("GetCursorPos")
	procSetForegroundWindow = user32DLL.NewProc("SetForegroundWindow")
	procLoadIconW           = user32DLL.NewProc("LoadIconW")
	procLoadCursorW         = user32DLL.NewProc("LoadCursorW")

	procGetModuleHandleW = kernel32DLL.NewProc("GetModuleHandleW")
	procShellNotifyIconW = shell32DLL.NewProc("Shell_NotifyIconW")
)

const (
	WM_DESTROY       = 0x0002
	WM_CLOSE         = 0x0010
	WM_COMMAND       = 0x0111
	WM_APP           = 0x8000
	WM_TRAYICON      = WM_APP + 1
	WM_RBUTTONUP     = 0x0205
	WM_LBUTTONUP     = 0x0202
	WM_LBUTTONDBLCLK = 0x0203

	NIM_ADD     = 0x00000000
	NIM_DELETE  = 0x00000002
	NIF_MESSAGE = 0x00000001
	NIF_ICON    = 0x00000002
	NIF_TIP     = 0x00000004

	MF_STRING    = 0x00000000
	MF_SEPARATOR = 0x00000800

	ID_TRAY_OPEN_POS   = 2001
	ID_TRAY_OPEN_ADMIN = 2002
	ID_TRAY_OPEN_LAN   = 2003
	ID_TRAY_EXIT       = 2004
)

type WNDCLASSEXW struct {
	cbSize        uint32
	style         uint32
	lpfnWndProc   uintptr
	cbClsExtra    int32
	cbWndExtra    int32
	hInstance     uintptr
	hIcon         uintptr
	hCursor       uintptr
	hbrBackground uintptr
	lpszMenuName  *uint16
	lpszClassName *uint16
	hIconSm       uintptr
}

type POINT struct {
	X int32
	Y int32
}

type NOTIFYICONDATAW struct {
	cbSize           uint32
	hWnd             uintptr
	uID              uint32
	uFlags           uint32
	uCallbackMessage uint32
	hIcon            uintptr
	szTip            [128]uint16
}

var (
	globalServerManager *ServerManager
	globalHwnd          uintptr
	globalNid           NOTIFYICONDATAW
)

func RunServerTrayMode(baseDir string) {
	runtime.LockOSThread()
	defer runtime.UnlockOSThread()

	sm := NewServerManager(baseDir, 8088)
	if err := sm.Start(); err != nil {
		showAppDialog("Gagal Memulai Server", fmt.Sprintf("Gagal menjalankan engine PHP internal: %v", err), 0x00000010)
		return
	}
	defer sm.Stop()
	globalServerManager = sm

	hInst, _, _ := procGetModuleHandleW.Call(0)
	className := utf16Ptr("NoderaKasirServerTrayClass")

	var wc WNDCLASSEXW
	wc.cbSize = uint32(unsafe.Sizeof(wc))
	wc.lpfnWndProc = syscall.NewCallback(trayWndProc)
	wc.hInstance = hInst
	wc.lpszClassName = className
	wc.hCursor, _, _ = procLoadCursorW.Call(0, 32512) // IDC_ARROW

	procRegisterClassExW.Call(uintptr(unsafe.Pointer(&wc)))

	hwnd, _, _ := procCreateWindowExW.Call(
		0,
		uintptr(unsafe.Pointer(className)),
		uintptr(unsafe.Pointer(utf16Ptr("Nodera Kasir Server"))),
		0,
		0, 0, 0, 0,
		0, 0, hInst, 0,
	)
	globalHwnd = hwnd

	// Setup Tray Icon
	hIcon, _, _ := procLoadIconW.Call(hInst, 1) // ID 1 from winres
	if hIcon == 0 {
		hIcon, _, _ = procLoadIconW.Call(0, 32512)
	}

	globalNid.cbSize = uint32(unsafe.Sizeof(globalNid))
	globalNid.hWnd = hwnd
	globalNid.uID = 100
	globalNid.uFlags = NIF_MESSAGE | NIF_ICON | NIF_TIP
	globalNid.uCallbackMessage = WM_TRAYICON
	globalNid.hIcon = hIcon

	tip := fmt.Sprintf("NODERA KASIR POS (Port %d)", sm.Port)
	tipU16, _ := syscall.UTF16FromString(tip)
	copy(globalNid.szTip[:], tipU16)

	procShellNotifyIconW.Call(NIM_ADD, uintptr(unsafe.Pointer(&globalNid)))
	defer procShellNotifyIconW.Call(NIM_DELETE, uintptr(unsafe.Pointer(&globalNid)))

	// Automatically open POS window
	appUrl := fmt.Sprintf("http://127.0.0.1:%d", sm.Port)
	_, _ = OpenAppWindow(appUrl)

	// Message Loop
	var msg struct {
		hwnd    uintptr
		message uint32
		wParam  uintptr
		lParam  uintptr
		time    uint32
		pt      POINT
	}

	for {
		ret, _, _ := procGetMessageW.Call(uintptr(unsafe.Pointer(&msg)), 0, 0, 0)
		if ret == 0 || int32(ret) == -1 {
			break
		}
		procTranslateMessage.Call(uintptr(unsafe.Pointer(&msg)))
		procDispatchMessageW.Call(uintptr(unsafe.Pointer(&msg)))
	}
}

func trayWndProc(hwnd uintptr, msg uint32, wParam, lParam uintptr) uintptr {
	switch msg {
	case WM_TRAYICON:
		if lParam == WM_RBUTTONUP {
			showTrayContextMenu(hwnd)
			return 0
		} else if lParam == WM_LBUTTONDBLCLK || lParam == WM_LBUTTONUP {
			if globalServerManager != nil {
				appUrl := fmt.Sprintf("http://127.0.0.1:%d", globalServerManager.Port)
				_, _ = OpenAppWindow(appUrl)
			}
			return 0
		}

	case WM_COMMAND:
		switch wParam {
		case ID_TRAY_OPEN_POS:
			if globalServerManager != nil {
				appUrl := fmt.Sprintf("http://127.0.0.1:%d", globalServerManager.Port)
				_, _ = OpenAppWindow(appUrl)
			}
		case ID_TRAY_OPEN_ADMIN:
			if globalServerManager != nil {
				adminUrl := fmt.Sprintf("http://127.0.0.1:%d/admin", globalServerManager.Port)
				_, _ = OpenAppWindow(adminUrl)
			}
		case ID_TRAY_OPEN_LAN:
			if globalServerManager != nil {
				lanIp := GetLocalLANIP()
				lanUrl := fmt.Sprintf("http://%s:%d", lanIp, globalServerManager.Port)
				_ = OpenBrowser(lanUrl)
			}
		case ID_TRAY_EXIT:
			procDestroyWindow.Call(hwnd)
		}
		return 0

	case WM_DESTROY:
		procPostQuitMessage.Call(0)
		return 0
	}

	ret, _, _ := procDefWindowProcW.Call(hwnd, uintptr(msg), wParam, lParam)
	return ret
}

func showTrayContextMenu(hwnd uintptr) {
	hMenu, _, _ := procCreatePopupMenu.Call()
	if hMenu == 0 {
		return
	}
	defer procDestroyMenu.Call(hMenu)

	var pt POINT
	procGetCursorPos.Call(uintptr(unsafe.Pointer(&pt)))

	procAppendMenuW.Call(hMenu, MF_STRING, ID_TRAY_OPEN_POS, uintptr(unsafe.Pointer(utf16Ptr("Buka Nodera Kasir POS"))))
	procAppendMenuW.Call(hMenu, MF_STRING, ID_TRAY_OPEN_ADMIN, uintptr(unsafe.Pointer(utf16Ptr("Buka Panel Admin"))))
	procAppendMenuW.Call(hMenu, MF_SEPARATOR, 0, 0)

	lanIp := GetLocalLANIP()
	port := 8088
	if globalServerManager != nil {
		port = globalServerManager.Port
	}
	lanText := fmt.Sprintf("Alamat LAN Mobile: http://%s:%d", lanIp, port)
	procAppendMenuW.Call(hMenu, MF_STRING, ID_TRAY_OPEN_LAN, uintptr(unsafe.Pointer(utf16Ptr(lanText))))
	procAppendMenuW.Call(hMenu, MF_SEPARATOR, 0, 0)
	procAppendMenuW.Call(hMenu, MF_STRING, ID_TRAY_EXIT, uintptr(unsafe.Pointer(utf16Ptr("Keluar / Tutup Server"))))

	procSetForegroundWindow.Call(hwnd)
	procTrackPopupMenu.Call(hMenu, 0x0002|0x0000, uintptr(pt.X), uintptr(pt.Y), 0, hwnd, 0)
}
