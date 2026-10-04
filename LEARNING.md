# James WP Lab 學習紀錄

更新日期：2026-10-04。此文件的程式現況由 Work 重新讀取外掛全部檔案確認。

## 外掛用途與版本

James WP Lab 是 WordPress 外掛開發教學練習，逐步學習 hooks、Shortcode、前台資源、PHP OOP 與安全輸出。目前版本為 **0.4.0**；標示最低 WordPress 6.0、PHP 7.4，文字網域為 `james-wp-lab`。CSS／JavaScript 未變更，enqueue 資源版本仍為 `0.3.0`。

## 目前檔案結構

```text
james-wp-lab/
├── james-wp-lab.php
├── LEARNING.md
├── includes/
│   ├── class-jtl-greeting.php
│   └── class-jtl-greeting-settings.php
└── assets/
    ├── css/jtl-shortcode.css
    └── js/jtl-shortcode.js
```

## 舊有功能與 WordPress 技術

- 管理後台通知：`admin_notices` 配合 `current_user_can( 'manage_options' )`，讓管理員確認外掛已載入；這不是自訂設定頁。
- `[jtl_greeting]` Shortcode：`add_shortcode()` 與 `shortcode_atts()`；支援 `name` 屬性。
- 名稱清理：`sanitize_text_field()`；清理後嚴格等於空字串時，使用可翻譯的「訪客」，因此字串 `"0"` 是有效名稱。
- 輸出安全：標題使用 `esc_html()`，ID 使用 `esc_attr()`，直接輸出的介面文字使用 `esc_html__()`；資料層預設名稱先用 `__()` 翻譯，組合後才 escaping。
- 前台資源：`wp_enqueue_scripts`、`wp_enqueue_style()`、`wp_enqueue_script()`、`plugin_dir_url()`。
- 條件式載入：只有 `is_singular()`、查詢物件為 `WP_Post` 且主內容包含 `[jtl_greeting]` 時載入 CSS／JS。這不保證模板、widget 或其他動態位置的 Shortcode 一定載入資源。
- 多元件：`wp_unique_id()` 為每組說明建立不同 ID。
- 直接存取防護：PHP 檔案檢查 `ABSPATH`。

## 使用方式

```text
[jtl_greeting]
[jtl_greeting name="James"]
[jtl_greeting name="0"]
```

目前 `[jtl_greeting]` 使用後台設定的預設名稱；未設定或清理後為空時顯示「你好，訪客！」。另外兩個範例分別顯示「你好，James！」與「你好，0！」，優先於後台設定。可在同一篇文章或頁面放入多個元件，按鈕各自控制自己的說明。

## CSS、JavaScript 與 ARIA

CSS 定義問候卡片、按鈕、說明區塊與鍵盤焦點樣式。原生 JavaScript 在 DOM ready 初始化各元件，點擊後切換說明的 `hidden`、按鈕文字與 `aria-expanded`。

按鈕的 `aria-controls` 對應說明元素的唯一 ID，初始為 `aria-expanded="false"`，說明元素帶有 `hidden`。此階段沒有 AJAX 或 REST 請求。

## 2026-10-04：第一個 PHP OOP 重構

今天新增 `includes/class-jtl-greeting.php` 與 `JTL_Greeting`，將原本全域函式中的 Shortcode rendering 與 enqueue 邏輯移到同一個類別。

- 主檔使用 `require_once` 載入類別並建立一個物件。
- 建構子以 `array( $this, '方法名稱' )` 註冊 object callbacks。
- 使用 public 方法供 WordPress 呼叫，以及 private `$plugin_url` 儲存外掛根 URL。
- 主檔傳入 `plugin_dir_url( __FILE__ )`，避免 class 放在 `includes` 後資源 URL 指向錯誤目錄。
- 不為物件存活額外建立 global instance；註冊的 callback 會持有物件參照。
- 保留版本、Shortcode 行為、HTML、CSS／JS、ARIA 與條件式載入；admin notice 維持全域函式架構。

已完成 PHP syntax、隔離前後比對及真實 WordPress CLI bootstrap 驗證。使用者亦已人工確認具名 Shortcode、訪客 fallback、CSS 與 JavaScript 互動正常。CLI 驗證不代表所有主題／外掛整合情境皆已測試。

## 2026-10-04：Settings API／Options API

第一階段 OOP 重構完成並由使用者人工驗收後，今天新增正式的「預設問候名稱」設定頁，版本由 `0.3.0` 升為 `0.4.0`。

### 在後台找到設定

以具備 `manage_options` 的使用者登入 WordPress，開啟 **設定 → James WP Lab**，輸入「預設問候名稱」，再按儲存按鈕。

- Option 名稱：`jtl_default_greeting_name`。
- Settings group：`jtl_greeting_settings`。
- 頁面 slug：`james-wp-lab`。
- Option 預設值：空字串；未儲存過時不會因外掛載入而建立資料列。

### 類別與 WordPress API

- 新增 `JTL_Greeting_Settings`，專責設定頁、設定註冊、sanitizer、section 與欄位 rendering。
- `admin_menu` 的物件 callback 使用 `add_options_page()`。
- `admin_init` 的物件 callback 使用 `register_setting()`、`add_settings_section()`、`add_settings_field()`。
- 主檔以 `require_once` 載入並各建立一次 Greeting 與 Settings 物件；不使用 global 保存 instance。
- 表單使用 `settings_fields()`、`do_settings_sections()`、`submit_button()`，提交至 WordPress `options.php`。
- `JTL_Greeting` 以 private 方法使用 `get_option( 'jtl_default_greeting_name', '' )` 讀取預設名稱；沒有直接 SQL、自訂保存 handler 或 `update_option()` 呼叫。

### Shortcode 名稱優先順序

1. 清理後非空的 `name` attribute。
2. 後台設定中的清理後非空字串。
3. 可翻譯的「訪客」。

具名 Shortcode 不會讀取此 option。缺少、空字串或清理後為空的 `name` 才讀設定。設定為空白或非字串時回到「訪客」。所有判空皆嚴格比較空字串，因此 attribute 與設定中的字串 `"0"` 都有效。

### 安全處理

- Capability：選單要求 `manage_options`，設定頁 callback 也再次檢查；WordPress 保存端點另執行 capability 檢查。
- Nonce：`settings_fields()` 產生 `jtl_greeting_settings-options` nonce；`options.php` 在更新前執行 `check_admin_referer()`，缺少或非法 nonce 不得進入更新流程。
- Sanitization：`register_setting()` 指定公開物件方法作為 sanitize callback；只接受字串並使用 `sanitize_text_field()`，array、object、integer 等非字串轉為空字串。
- 讀取防禦：Greeting 再確認 option 為字串並清理；設定欄位對非字串使用空值。
- Escaping：欄位 `value` 使用 `esc_attr()`，Shortcode 最終標題使用 `esc_html()`；資料層訪客預設值仍先用 `__()` 翻譯。
- 沒有新增 REST、AJAX、自訂資料表或其他使用者功能。

### Work 驗收與限制

- 三個 PHP 檔案的 `php -l` 通過，使用 PHP CLI 8.4.24；程式碼維持 PHP 7.4 相容語法，但未使用 7.4 interpreter 實測。
- 真實 WordPress 7.1.2 bootstrap：外掛啟用、兩個 class 載入，Settings API option／allowlist／表單與各物件 hooks 驗證通過。
- 13 個 Shortcode 案例通過，包括具名優先、空白、`"0"`、非字串與 HTML／特殊字元。
- 真實 `sanitize_option()`、欄位 escaping、無權限頁面拒絕及核心 nonce 的有效／非法／缺少案例通過。
- CSS／JS 檔案 hashes、admin notice、enqueue 方法及原 HTML／escaping 區段與修改前相同。
- 測試使用記憶體中的 option filter，阻擋資料庫寫入並略過其他外掛、主題與持久化快取。正式 option 測試前後都不存在，沒有為測試建立或永久更新資料。
- 尚未以 HTTP／瀏覽器提交設定保存，未執行完整保存端點或所有外掛／主題整合測試；等待使用者人工驗收。

## 尚未完成與後續課程

- 本章設定頁的瀏覽器人工驗收，包括設定保存及重新載入。
- Filter Hook 的自訂擴充點。
- 外掛卸載生命週期與設定清理策略。
- AJAX／REST API、自訂資料表與更進階 OOP；留待後續分章學習。

下一個尚未實作的技術章節建議為「Filter Hook：提供問候文字的自訂擴充點」。目前仍沒有 REST API、AJAX 或自訂資料表功能；本次不開始下一章。
