# James WP Lab：AI Agent、Git 與人工批准規範

本文件於 2026-10-04 依 James 明確授權建立。適用範圍僅為 `james-wp-lab` 外掛 repository。

## 文件分工

- `LEARNING.md`：學習進度、WordPress／PHP 技術、實作歷程與驗收紀錄。
- `AI-WORKFLOW.md`：AI Agent、Git、Commit Gate 與 Approval 工作規範。
- 不得將工作授權或版本控制規則混入學習紀錄來取代本文件。

## 角色與責任

| 角色 | 責任 |
| --- | --- |
| Work = Supervisor | 讀取真實現況、審核 Planner 方案、限定 Builder 範圍、獨立測試、回報 PASS／FAIL、執行 Final Gate 與經授權的 Git 操作 |
| OpenClaw = Planner | 提出開發方案；規劃階段不得修改檔案、commit 或 push |
| Hermes = Builder | 依 Work 核定規格與 James 授權實作；不得自行 commit、push、擴大範圍或在完成後自行第二次修正 |
| James = Human Final Approval | 執行瀏覽器／人工驗收，明確表示對應版本 PASS；另行批准 push、merge 或其他需要批准的操作 |

## Git 基準與分支模型

Git 工作流程以 <https://github.com/ctka-james/git-init> 的 `main / docs / develop / feat` 模型為基準，不建立互相衝突的分支流程。

- `main`：已核准的穩定基準；不得直接進行一般功能開發。
- `docs`：文件分支。
- `develop`：開發整合分支。
- `feat`：日常功能開發分支，預設工作分支。
- 未經 James 明確批准，不得 merge `feat → develop` 或 `develop → main`，也不得自行 rebase、reset、clean 或以 checkout 覆蓋工作內容。
- 不得自行建立額外 branch。
- 本次第一次 `0.4.0` baseline 是 James 明確批准的初始化例外：先在 `main` 建立一次 baseline commit，再讓另外三個 branch 指向同一 commit，最後停在 `feat`。這不是允許在 `main` 做日常開發。
- 不直接執行遠端腳本。取得工具時先 clone／read／review；工具中的自動 commit、套件安裝或 global config 變更不會自動取得授權。

## Conventional Commits 與繁體中文

格式：`type(scope): 繁體中文摘要`。Subject 必須清楚描述實際變更，空一行後用繁體中文條列主要變更。Scope 使用有意義的功能或維護範圍。

至少允許：`feat`（新增功能）、`fix`（修正錯誤）、`refactor`（不改變功能的重構）、`docs`（文件）、`test`（測試）、`chore`（維護／工具／設定）、`style`（不影響功能的格式調整）。其他上游 Conventional Commits 類型應依實際用途審核，不另建衝突規範。

禁止沒有意義的訊息，例如單獨的 `update`、`fix`、`changes`、`修改`、`test`，或只有類型卻無具體描述。不得寫入未出現在 staged 內容中的功能、修改或測試宣稱。

```text
feat(settings): 新增 James WP Lab 預設問候名稱設定

- 新增 Settings API 設定頁
- 加入預設問候名稱 option 與 Shortcode fallback
- 補充 nonce、capability 與 sanitization 驗證
```

Commitlint／hook／template 是格式輔助，不能取代人工批准。不得宣稱尚未安裝的 hook 已提供 enforcement；繁體中文、必填 body 與人工 PASS 都需要 Work 明確檢查。

## Commit Gate

未來程式變更必須依序：

1. OpenClaw Planner。
2. Work Review。
3. Hermes Builder。
4. Work Test，並回報 PASS／FAIL。
5. James 瀏覽器／人工驗收。
6. James 明確表示對應版本與內容 PASS。
7. Work Final Gate，產生並顯示繁體中文 Conventional Commit。
8. 建立 local commit。

Hermes 完成、PHP syntax PASS、Work tests PASS 或 WordPress Runtime PASS 均不能取代 James 的人工批准。未經人工 PASS 不得 commit。

人工 PASS 後若程式再發生變更，舊 PASS 不涵蓋新的差異。Work 必須回報新增差異，完成必要測試並重新取得 James 對新內容的人工 PASS。

本次初始化所需的本文件與 `.gitignore` 由 James 明確授權 Work 建立；不得藉此修改已驗收的外掛程式或擴大 baseline 範圍。

## Commit 前 Final Gate

- 執行 `git status`，確認沒有非預期檔案。
- 檢查 `git diff` 與 `git diff --cached`；第一次 commit 沒有 parent，改以完整 staged file list 與 staged content 審查。
- `git add` 前先檢查候選檔案、`.gitignore`、secrets、`.env`、credentials、API keys、tokens、logs、cache 與 temporary files；`.gitignore` 不取代內容審查。
- Staging 後確認完整檔案清單與內容，且與已驗收內容一致；不能因只是 staging 就略過檢查。
- 確認 branch 正確、修改範圍已授權，沒有將 WordPress Core、其他 plugin／theme 或資料庫／系統資料納入。
- 檢查 commit identity；沒有 James 提供或明確批准的 identity 時停止，不猜測姓名或 email，不冒用 identity。
- 根據真正 staged 內容撰寫繁體中文 subject／body，先顯示給 James。
- 確認 James 的人工 PASS 對應目前內容，且沒有較新的禁止 commit 指示。

## Push Gate

- `git commit` 與 `git push` 是兩個不同授權。
- 人工 PASS 可以授權依規範建立 local commit，不代表允許 push。
- 未經 James 明確批准，不得 push、建立 GitHub repository 或設定 remote。
- 不得自行 `git push --force` 或 `git push --force-with-lease`；只有 James 針對該次操作明確批准時才可執行。
- Merge 與 push 的批准互不取代。

## PASS／FAIL 與停止規則

- Work PASS：回報證據與未驗證項目，等待 James 人工驗收；不立即 commit。
- Work FAIL：回報問題、影響與已執行操作，停止並等待 James 決定；不得自行無限重試、第二次呼叫 Hermes 或擴大修復範圍。
- 無法安全驗證：標記未驗證／INCONCLUSIVE，不把推測當 PASS，不為取得 PASS 改變未授權的網站或資料庫狀態。
- 已授權的 local commit 完成後，回報 SHA、branches 與工作區狀態，停止；不自動 push、部署或開始下一章。
