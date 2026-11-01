package main

import (
	"fmt"
	"net"
	"os"
	"os/exec"
	"path/filepath"
	"strings"
	"syscall"
	"time"
	"unsafe"
)

type ServerManager struct {
	BaseDir string
	PhpBin  string
	DocRoot string
	Port    int
	Cmd     *exec.Cmd
	Running bool
}

func utf16Ptr(s string) *uint16 {
	p, _ := syscall.UTF16PtrFromString(s)
	return p
}

func unsafePointer(p *uint16) uintptr {
	return uintptr(unsafe.Pointer(p))
}

func FindPhpBinary(baseDir string) string {
	candidates := []string{
		filepath.Join(baseDir, "bin", "php.exe"),
		filepath.Join(baseDir, "bin", "php-win.exe"),
		filepath.Join(baseDir, "bin", "php-win", "php.exe"),
		filepath.Join(baseDir, "bin", "php", "php.exe"),
		filepath.Join(baseDir, "php", "php.exe"),
		filepath.Join(baseDir, "..", "bin", "php.exe"),
		`C:\laragon\bin\php\php.exe`,
		`C:\xampp\php\php.exe`,
	}

	for _, c := range candidates {
		if fi, err := os.Stat(c); err == nil && !fi.IsDir() {
			return c
		}
	}

	if sysPhp, err := exec.LookPath("php.exe"); err == nil {
		return sysPhp
	}
	if sysPhp, err := exec.LookPath("php"); err == nil {
		return sysPhp
	}

	return ""
}

func FindDocRoot(baseDir string) string {
	candidates := []string{
		filepath.Join(baseDir, "public"),
		filepath.Join(baseDir, "..", "public"),
		baseDir,
	}

	for _, c := range candidates {
		idx := filepath.Join(c, "index.php")
		if fi, err := os.Stat(idx); err == nil && !fi.IsDir() {
			return c
		}
	}
	return baseDir
}

func GetLocalLANIP() string {
	addrs, err := net.InterfaceAddrs()
	if err != nil {
		return "127.0.0.1"
	}
	for _, a := range addrs {
		if ipNet, ok := a.(*net.IPNet); ok && !ipNet.IP.IsLoopback() {
			if ipNet.IP.To4() != nil {
				ipStr := ipNet.IP.String()
				if strings.HasPrefix(ipStr, "192.168.") || strings.HasPrefix(ipStr, "10.") || strings.HasPrefix(ipStr, "172.") {
					return ipStr
				}
			}
		}
	}
	return "127.0.0.1"
}

func ConfigurePhpIni(phpBin, baseDir string) {
	phpDir := filepath.Dir(phpBin)
	iniPath := filepath.Join(phpDir, "php.ini")
	extDir := filepath.Join(phpDir, "ext")

	data, err := os.ReadFile(iniPath)
	if err != nil {
		return
	}
	content := string(data)

	extDirClean := filepath.Clean(extDir)
	lines := strings.Split(content, "\n")
	var newLines []string
	extDirSet := false

	for _, line := range lines {
		trimmed := strings.TrimSpace(line)
		if strings.HasPrefix(trimmed, "extension_dir") {
			newLines = append(newLines, fmt.Sprintf("extension_dir = \"%s\"", extDirClean))
			extDirSet = true
		} else {
			newLines = append(newLines, line)
		}
	}

	if !extDirSet {
		newLines = append(newLines, fmt.Sprintf("extension_dir = \"%s\"", extDirClean))
	}

	_ = os.WriteFile(iniPath, []byte(strings.Join(newLines, "\n")), 0644)
}

func IsPortAvailable(port int) bool {
	ln, err := net.Listen("tcp", fmt.Sprintf("0.0.0.0:%d", port))
	if err != nil {
		return false
	}
	_ = ln.Close()
	return true
}

func FindAvailablePort(startPort int) int {
	for p := startPort; p < startPort+100; p++ {
		if IsPortAvailable(p) {
			return p
		}
	}
	return startPort
}

func NewServerManager(baseDir string, defaultPort int) *ServerManager {
	phpBin := FindPhpBinary(baseDir)
	docRoot := FindDocRoot(baseDir)
	port := FindAvailablePort(defaultPort)

	return &ServerManager{
		BaseDir: baseDir,
		PhpBin:  phpBin,
		DocRoot: docRoot,
		Port:    port,
	}
}

func (s *ServerManager) Start() error {
	if s.Running {
		return nil
	}

	if s.PhpBin == "" {
		return fmt.Errorf("PHP executable tidak ditemukan di direktori bin/.")
	}

	ConfigurePhpIni(s.PhpBin, s.BaseDir)

	phpDir := filepath.Dir(s.PhpBin)
	iniPath := filepath.Join(phpDir, "php.ini")

	args := []string{
		"-c", iniPath,
		"-S", fmt.Sprintf("0.0.0.0:%d", s.Port),
		"-t", s.DocRoot,
	}

	cmd := exec.Command(s.PhpBin, args...)
	cmd.Dir = s.DocRoot

	// Hide Windows CMD Console Window
	cmd.SysProcAttr = &syscall.SysProcAttr{
		CreationFlags: 0x08000000 | 0x00000200, // CREATE_NO_WINDOW | CREATE_NEW_PROCESS_GROUP
		HideWindow:    true,
	}

	if err := cmd.Start(); err != nil {
		return fmt.Errorf("gagal memulai PHP server: %v", err)
	}

	s.Cmd = cmd
	s.Running = true

	// Wait up to 3 seconds for port to open
	addr := fmt.Sprintf("127.0.0.1:%d", s.Port)
	for i := 0; i < 30; i++ {
		conn, err := net.DialTimeout("tcp", addr, 100*time.Millisecond)
		if err == nil {
			_ = conn.Close()
			break
		}
		time.Sleep(100 * time.Millisecond)
	}

	return nil
}

func (s *ServerManager) Stop() {
	if !s.Running || s.Cmd == nil || s.Cmd.Process == nil {
		return
	}

	_ = s.Cmd.Process.Kill()
	s.Running = false
}

func OpenBrowser(url string) error {
	cmd := exec.Command("rundll32", "url.dll,FileProtocolHandler", url)
	cmd.SysProcAttr = &syscall.SysProcAttr{HideWindow: true}
	return cmd.Start()
}

func OpenAppWindow(url string) (*exec.Cmd, error) {
	candidates := []struct {
		path string
		args []string
	}{
		{
			path: os.Getenv("ProgramFiles(x86)") + `\Microsoft\Edge\Application\msedge.exe`,
			args: []string{fmt.Sprintf("--app=%s", url), "--window-size=1280,820"},
		},
		{
			path: os.Getenv("ProgramFiles") + `\Microsoft\Edge\Application\msedge.exe`,
			args: []string{fmt.Sprintf("--app=%s", url), "--window-size=1280,820"},
		},
		{
			path: os.Getenv("LocalAppData") + `\Microsoft\Edge\Application\msedge.exe`,
			args: []string{fmt.Sprintf("--app=%s", url), "--window-size=1280,820"},
		},
		{
			path: os.Getenv("ProgramFiles") + `\Google\Chrome\Application\chrome.exe`,
			args: []string{fmt.Sprintf("--app=%s", url), "--window-size=1280,820"},
		},
		{
			path: os.Getenv("ProgramFiles(x86)") + `\Google\Chrome\Application\chrome.exe`,
			args: []string{fmt.Sprintf("--app=%s", url), "--window-size=1280,820"},
		},
	}

	for _, c := range candidates {
		if fi, err := os.Stat(c.path); err == nil && !fi.IsDir() {
			cmd := exec.Command(c.path, c.args...)
			if err := cmd.Start(); err == nil {
				return cmd, nil
			}
		}
	}

	// Fallback to default browser
	return nil, OpenBrowser(url)
}
