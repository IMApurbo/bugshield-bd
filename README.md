# BugShield BD - Bug Bounty Platform (Laravel)

Roles: **Researcher**, **Organization**, **Admin**.

## Run on Kali Linux
```bash
unzip bugshield-bd.zip && cd bugshield-bd
chmod +x setup.sh
./setup.sh            # installs PHP, Composer, MariaDB (MySQL); creates DB + Laravel app; migrates, seeds, serves
```
Database: MySQL (MariaDB). Setup creates database `bugshield` and user `bugshield` (override with `DB_NAME`, `DB_USER`, `DB_PASS` env vars; credentials are written to `bugshield-app/.env`).

Open http://127.0.0.1:8000 . Needs internet during setup (apt + Composer) and in the browser (Bootstrap/jQuery/Chart.js CDN).

Set your real bKash number before setup: `BKASH_NUMBER=017XXXXXXXX ./setup.sh`
(or edit `BKASH_NUMBER` in `bugshield-app/.env` later).

## Demo accounts
| Role | Email | Password |
|---|---|---|
| Admin | admin@bugshield.bd | Admin@12345 |
| Researcher | researcher@demo.bd | password |
| Organization | org@demo.bd | password |

## Organization registration (BDT 20,000 via bKash)
1. Org opens **Register organization**; the form shows the bKash number and the BDT 20,000 fee.
2. Org sends the money, then enters the sender number and the TrxID on the form.
3. Account is created as **pending**; the org sees a "waiting for approval" page and cannot publish programs.
4. Admin opens **Organizations**, checks the TrxID in the bKash app, then clicks **Approve** (or **Reject** with a reason).
5. After approval the org can create programs and triage reports.

TrxIDs are unique, so one payment cannot be reused for two registrations. Fee amount: `config/bugshield.php`.

## Researcher withdrawal
Wallet page: enter bKash number + amount. The balance is debited, the withdrawal is recorded (reference `WDxxxxxxxx`) and
"Withdrawal successful" is shown immediately. **This is a simulation: no money is actually sent.** Real payouts
need the bKash Disbursement API (merchant agreement) or manual sending by the admin from the Withdrawals list.

## What is implemented (from the proposal)
Laravel MVC + Eloquent, role-based access (middleware + per-report authorization), CSRF, validation, bcrypt, login throttling,
CVSS v3.1 calculator (jQuery live + PHP server-side), report submission with evidence upload, duplicate-detection Job,
triage workflow (new, triaged, resolved, duplicate, informative), threaded comments, reward tiers, reputation points,
leaderboard, Hall of Fame, admin analytics (Chart.js + AJAX), user suspension, audit log, AJAX withdrawal.

## Not included (phase 2 in the proposal)
Sanctum REST API, Redis/Horizon, Scout/Meilisearch, SMS/OTP, KYC, Socialite, DomPDF certificates, webhooks, Docker/CI.
Org-side prepaid reward funding is not modelled: resolved rewards are credited to the researcher wallet.
