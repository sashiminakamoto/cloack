# Yellow Cloaker v3.0 - YOURLS Integration

**Release Date:** November 29, 2025

## 🆕 What's New in Version 3.0

### Major Features

#### 1. **YOURLS Integration** 🔗
- Integrated YOURLS (Your Own URL Shortener) for creating custom short links
- Bot protection automatically applied to all short links
- Separate instances:
  - `/yourls/` - Original YOURLS without cloaking
  - `/links/` - **NEW** YOURLS with full bot protection

#### 2. **Smart Traffic Filtering** 🛡️
When someone clicks a short link (`https://yourdomain.com/links/abc`):
- **Real Users** → Redirected to original URL ✅
- **Bots/Crawlers** → Redirected to safe page (customizable) ❌

#### 3. **Advanced Detection** 🔍
- **User-Agent Filtering**: Block specific bots (Facebook, curl, wget, etc.)
- **Country-Based Filtering**: Allow/block by country codes
- **ISP Filtering**: Block cloud providers, VPNs, data centers
- **IP Blacklist**: Built-in database + custom IP lists
- **VPN/Tor Detection**: Optional VPN/Tor blocking via ipinfo.app API

#### 4. **Comprehensive Logging** 📊
- **White Clicks**: Blocked traffic (bots) logged with reason
- **Black Clicks**: Legitimate traffic allowed through
- Real-time statistics via admin dashboard
- JSON-based logging with SleekDB

---

## 📦 Installation

### Requirements
- PHP 7.2+ (recommended: 8.1)
- MySQL/MariaDB
- Nginx or Apache
- SSL certificate (HTTPS required)

### Quick Start

1. **Clone Repository**
```bash
git clone https://github.com/sashiminakamoto/cloack.git
cd cloack
```

2. **Configure Database** (for YOURLS)
```bash
mysql -u root -p
CREATE DATABASE yourls;
CREATE USER 'yourls'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON yourls.* TO 'yourls'@'localhost';
FLUSH PRIVILEGES;
```

3. **Setup YOURLS**
```bash
cp links/user/config-sample.php links/user/config.php
nano links/user/config.php
# Edit database credentials and site URL
```

4. **Configure Cloaker**
```bash
cp settings-sample.json settings.json
nano settings.json
# Configure filters (see Configuration section)
```

5. **Set Permissions**
```bash
chown -R www-data:www-data /path/to/cloack
chmod 755 -R /path/to/cloack
chmod 777 logs/
```

---

## ⚙️ Configuration

### Filter Configuration (`settings.json`)

```json
{
  "tds": {
    "mode": "on",
    "filters": {
      "allowed": {
        "countries": ["VN", "TH", "US"],  // Allowed countries
        "os": [],                          // Empty = allow all OS
        "languages": ["any"]               // Allow all languages
      },
      "blocked": {
        "useragents": [                    // Blocked user agents
          "facebook",
          "curl",
          "bot",
          "spider"
        ],
        "isps": [                          // Blocked ISPs
          "Amazon",
          "Google Cloud",
          "DigitalOcean"
        ],
        "vpntor": false                    // VPN/Tor blocking
      }
    }
  }
}
```

### White Page Configuration

Set where bots are redirected:

```json
{
  "white": {
    "action": "redirect",              // or "folder", "curl", "error"
    "redirect": {
      "urls": ["https://example.com"],
      "type": "302"
    }
  }
}
```

---

## 🎯 Usage

### Creating Short Links

1. **Access YOURLS Admin**
```
https://yourdomain.com/links/admin/
```

2. **Create Link**
- Enter original URL: `https://mysite.com/offer`
- Custom keyword (optional): `offer123`
- Click "Shorten The URL"

3. **Result**
```
Short URL: https://yourdomain.com/links/offer123
```

### Traffic Flow

```
User clicks: https://yourdomain.com/links/offer123
           ↓
    Cloaker Check
           ↓
    ┌──────────┴──────────┐
    │                     │
  BOT                 REAL USER
    │                     │
    ↓                     ↓
Redirect to         Redirect to
safe page           https://mysite.com/offer
(Tinhte.vn)         ✅ Original URL
```

### Viewing Statistics

**Cloaker Stats** (bot detection):
```
https://yourdomain.com/admin?password=12345
```

**YOURLS Stats** (click tracking):
```
https://yourdomain.com/links/admin/
```

---

## 🔧 Advanced Features

### Country Codes
```
VN - Vietnam          US - United States
TH - Thailand         GB - United Kingdom
SG - Singapore        JP - Japan
MY - Malaysia         KR - South Korea
ID - Indonesia        AU - Australia
```

### Detection Modes

**1. All Traffic → White** (moderation mode)
```json
"tds": { "mode": "full" }
```

**2. All Traffic → Black** (testing mode)
```json
"tds": { "mode": "off" }
```

**3. Normal Filtering**
```json
"tds": { "mode": "on" }
```

---

## 📊 Architecture

### Directory Structure
```
/
├── index.php              # Main cloaker entry
├── core.php               # Detection engine
├── settings.json          # Configuration
├── bases/                 # IP/ISP databases
│   ├── bots.txt          # Bot IP ranges (CIDR)
│   └── ipcountry.php     # GeoIP detection
├── links/                 # YOURLS with cloaker
│   ├── index.php         # Bot check wrapper
│   ├── yourls-loader.php # YOURLS core
│   ├── admin/            # Admin panel
│   └── logs/             # Detection logs
└── yourls/                # Original YOURLS (no cloaker)
```

### Detection Logic (core.php)

```php
class Cloaker {
  public function check() {
    // 1. IP blacklist check
    // 2. VPN/Tor check (optional)
    // 3. User-Agent check
    // 4. OS check
    // 5. Country check
    // 6. Language check
    // 7. Referer check
    // 8. URL token check
    // 9. ISP check

    return $result; // 0 = allow, 1 = block
  }
}
```

---

## ⚠️ Legal Disclaimer

**This tool is for educational and defensive security purposes only.**

### ✅ Legitimate Use Cases:
- Protecting your website from scraping
- Blocking competitor spy tools
- Filtering malicious bots
- A/B testing with traffic segmentation
- Research and education

### ❌ Prohibited Uses:
- Violating advertising network Terms of Service
- Deceiving human users
- Cloaking for black-hat SEO
- Any illegal activities

**The author is NOT responsible for misuse of this software.**

---

## 🛡️ Security Notes

### Do NOT Commit These Files:
```
settings.json           # Your config
links/user/config.php   # Database credentials
logs/*.json             # Real traffic data
*.backup                # Backup files
```

### Change Default Passwords:
```bash
# YOURLS admin
nano links/user/config.php
# Line: 'admin' => '12345' → Change password

# Cloaker admin
nano settings.json
# "password": "12345" → Change password
```

---

## 📝 Changelog

### v3.0 (2025-11-29)
- ✨ NEW: YOURLS integration with bot protection
- ✨ NEW: Separate cloaked (`/links/`) and non-cloaked (`/yourls/`) instances
- 🔧 Improved: Modular filter system
- 🔧 Improved: JSON-based logging with SleekDB
- 📊 NEW: Real-time statistics dashboard

### v2.0
- Country-based filtering
- ISP filtering
- Custom white pages per domain

### v1.0
- Initial release
- Basic bot detection
- IP blacklist

---

## 🙏 Credits

- **Base Cloaker**: [Yellow Web](https://yellowweb.top)
- **YOURLS**: [yourls.org](https://yourls.org)
- **MaxMind**: GeoIP databases
- **SleekDB**: File-based NoSQL database

---

## 📞 Support

- **GitHub Issues**: https://github.com/sashiminakamoto/cloack/issues
- **Original Yellow Cloaker**: https://github.com/dvygolov/YellowCloaker

---

## 📄 License

MIT License - See LICENSE file for details

---

**⚡ Version 3.0 - Built with bot protection in mind**
