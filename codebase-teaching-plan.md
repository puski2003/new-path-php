# Codebase Teaching Plan

## Goal

Understand this repo well enough to:

- explain the request flow to teammates
- survive a code check where you must add another CRUD or change behavior
- spot where the code is clean versus where the vibe-coded mess starts
- use AI in small safe batches without overwhelming it

This document is not a rewrite plan. It is a study plan plus a practice board.

## The Real Mental Model

Ignore the mess for a moment. The main app shape is still:

1. `public/index.php` receives the request.
2. The URL maps to `pages/<route>/index.php`.
3. That page usually loads a role guard from `pages/<role>/common/*.head.php`.
4. Then it usually loads:
   - `*.controller.php`
   - `*.model.php`
   - `*.layout.php`
5. Shared partials live in `pages/<role>/common/`.
6. Shared CSS is loaded from `pages/<role>/common/*html.head.php`.
7. Shared JS is loaded from `pages/<role>/common/*footer.php`.
8. Feature CSS and JS live under `public/assets/css/...` and `public/assets/js/...`.

That is the architecture you should teach first.

## What Is Messy Right Now

These are the main repo problems worth teaching explicitly:

- some pages use the shared `$pageScripts` and `$pageStyle` pattern, others hardcode scripts directly in layouts
- some pages keep controller logic, layout markup, and browser JavaScript all inside one `index.php`
- some JavaScript files contain placeholder behavior or routes that do not appear to exist
- some layouts still contain inline JavaScript
- some CSS crosses role boundaries, for example counselor CSS importing user CSS
- there are repeated UI patterns for chat, follow-up, filters, toggles, and notifications

Teach the clean pattern first. Then show where the repo breaks its own rules.

## Team Study Rules

Use these rules in every study session:

1. Never ask AI to scan the whole repo at once.
2. Only give AI 4 to 6 files per prompt.
3. First map read flow, then write flow, then assets, then mess.
4. Separate PHP responsibilities:
   - route entry
   - auth/guard
   - controller
   - model
   - layout
   - partials
   - CSS
   - JS
5. For every feature, write one mini map:
   - route
   - files touched
   - DB writes
   - forms
   - AJAX endpoints
   - shared partials
6. When you find messy code, do not jump to refactor it immediately. First explain what it is doing today.

## Step-By-Step Teaching Plan

### Step 1: Learn the Shell

Goal: understand how the app boots and how assets are loaded.

Read these files first:

- `public/index.php`
- `pages/user/common/user.head.php`
- `pages/user/common/user.html.head.php`
- `pages/user/common/user.footer.php`
- `pages/counselor/common/counselor.head.php`
- `pages/admin/common/admin.head.php`

What the team must be able to explain:

- how a route becomes a file
- how auth is enforced
- how page CSS is declared
- how page JS is declared
- where shared partials come from

### Step 2: Learn One Clean Feature End To End

Use the user dashboard first because it mostly follows the expected pattern.

Read:

- `pages/user/dashboard/index.php`
- `pages/user/dashboard/dashboard.controller.php`
- `pages/user/dashboard/dashboard.model.php`
- `pages/user/dashboard/dashboard.layout.php`
- `public/assets/js/user/dashboard.js`
- `public/assets/css/user/dashboard.css`

What the team must explain:

- where dashboard data comes from
- which variables are created in the controller
- how the layout consumes them
- which forms submit
- which JS behavior is real versus fake

### Step 3: Learn One Feature With Many Subroutes

Use recovery because it is close to the kind of task lecturers ask during a code check.

Core files:

- `pages/user/recovery/index.php`
- `pages/user/recovery/recovery.controller.php`
- `pages/user/recovery/recovery.model.php`
- `pages/user/recovery/recovery.layout.php`
- `public/assets/js/user/recovery.js`
- `public/assets/css/user/recovery.css`

Then read subroutes in small batches:

- `pages/user/recovery/checkin/index.php`
- `pages/user/recovery/log-urge/index.php`
- `pages/user/recovery/journal/index.php`
- `pages/user/recovery/journal/write/index.php`
- `pages/user/recovery/task/complete/index.php`
- `pages/user/recovery/manage/index.php`
- `pages/user/recovery/browse/index.php`
- `pages/user/recovery/accept/index.php`
- `pages/user/recovery/reject/index.php`
- `pages/user/recovery/resume/index.php`

What the team must explain:

- which recovery actions are separate routes
- which routes are read routes and which are write routes
- how the recovery page reuses partials
- which UI actions are real and which are placeholder UI

### Step 4: Learn A Shared-Partial Heavy Feature

Use community and profile pages.

Read:

- `pages/user/community/community.controller.php`
- `pages/user/community/community.layout.php`
- `public/assets/js/user/community/community.js`
- `public/assets/js/user/community/chat.js`
- `pages/user/profile/index.php`
- `pages/user/profile/profile.layout.php`

What the team must explain:

- how one page loads multiple JS files
- how modal and chat behavior are wired
- which layout files still bypass the shared footer pattern

### Step 5: Learn Counselor Pages

Pick one scheduling feature and one recovery-plan feature.

Batch A:

- `pages/counselor/sessions/index.php`
- `pages/counselor/sessions/sessions.controller.php`
- `pages/counselor/sessions/sessions.layout.php`
- `public/assets/js/counselor/sessions.js`
- `public/assets/js/counselor/followUp.js`
- `public/assets/js/counselor/rescheduleRequests.js`

Batch B:

- `pages/counselor/recovery-plans/create/index.php`
- `pages/counselor/recovery-plans/create/create.controller.php`
- `pages/counselor/recovery-plans/create/create.layout.php`
- `public/assets/js/counselor/createRecoveryPlan.js`
- `public/assets/css/counselor/createRecoveryPlan.css`
- `public/assets/css/counselor/base.css`

What the team must explain:

- how counselor pages reuse their role shell
- where recovery-plan creation logic lives
- why `public/assets/css/counselor/base.css` importing `../user/dashboard.css` is an architecture smell

### Step 6: Learn One Admin CRUD Flow

The admin area is likely where a code check will ask for another CRUD.

Start with one real flow:

- `pages/admin/resources/index.php`
- `pages/admin/resources/resources.controller.php`
- `pages/admin/resources/resources.layout.php`
- `pages/admin/help-center/add/index.php`
- `pages/admin/help-center/edit/index.php`
- `pages/admin/help-center/delete/index.php`

Then repeat the same pattern with:

- `pages/admin/job-posts/add/index.php`
- `pages/admin/job-posts/edit/index.php`
- `pages/admin/job-posts/delete/index.php`

What the team must explain:

- how add, edit, and delete are split into separate routes
- where validation happens
- where redirects happen after writes
- how a new CRUD would fit the same folder pattern

### Step 7: Do A Mess Audit

Only do this after everyone understands the intended architecture.

Use these files as the first audit pack:

- `pages/user/profile/profile.layout.php`
- `pages/user/counselors/counselors.layout.php`
- `pages/user/sessions/follow-up/index.php`
- `public/assets/js/user/dashboard.js`
- `public/assets/js/user/recovery.js`
- `public/assets/css/counselor/base.css`

What the team must classify:

- broken wiring
- dead endpoints
- placeholder UI
- inline JS
- mixed loading patterns
- cross-role coupling

## The Prompt Ladder For AI

Do not ask AI to understand and fix everything in one prompt. Use this sequence.

### Prompt 1: Understand The Batch

```text
You are helping me understand a messy PHP codebase.

Only inspect these files:
<PASTE 4-6 FILES HERE>

Explain in simple English:
1. What route or feature these files belong to
2. The flow from route entry to controller/model to layout to partials
3. Which variables are created and where they are used
4. Which forms or AJAX calls write data
5. Which CSS and JS files are loaded

Rules:
- Do not scan the whole repo
- Do not refactor yet
- If more files are needed, ask only for the next batch
- Use short bullets
```

### Prompt 2: Find The Mess

```text
Now inspect only the same files again.

Tell me:
1. Which parts follow the repo pattern
2. Which parts break the pattern
3. Which code looks duplicated, placeholder, dead, or risky
4. Which changes would be low-risk cleanup versus risky refactor

Do not implement anything yet.
```

### Prompt 3: Prepare A Code Check Change

```text
Pretend my lecturer asked me to change this feature.

Based only on these files:
<PASTE 4-6 FILES HERE>

Give me:
1. The minimum files I would likely need to edit
2. The write path and redirect path
3. The safest order to make the change
4. The top 3 regression risks
5. A 10 minute explanation I can give my team
```

### Prompt 4: Implement In A Small Scope

```text
Implement only this small change:
<ONE CHANGE ONLY>

You may only modify these files:
<PASTE FILE LIST>

Before editing:
1. Restate the current flow
2. State the exact change
3. List anything that looks dead or unrelated but should not be changed

Then make the smallest safe patch.
```

## Role-Based Study Batches

### User Role Batches

Batch 1:

- `public/index.php`
- `pages/user/common/user.head.php`
- `pages/user/common/user.html.head.php`
- `pages/user/common/user.footer.php`

Batch 2:

- `pages/user/dashboard/index.php`
- `pages/user/dashboard/dashboard.controller.php`
- `pages/user/dashboard/dashboard.model.php`
- `pages/user/dashboard/dashboard.layout.php`
- `public/assets/js/user/dashboard.js`
- `public/assets/css/user/dashboard.css`

Batch 3:

- `pages/user/recovery/index.php`
- `pages/user/recovery/recovery.controller.php`
- `pages/user/recovery/recovery.layout.php`
- `public/assets/js/user/recovery.js`
- `pages/user/common/user.daily-tasks.php`
- `pages/user/common/user.progress-tracker.php`

Batch 4:

- `pages/user/community/community.controller.php`
- `pages/user/community/community.layout.php`
- `public/assets/js/user/community/community.js`
- `public/assets/js/user/community/chat.js`
- `pages/user/profile/index.php`
- `pages/user/profile/profile.layout.php`

### Counselor Role Batches

Batch 1:

- `pages/counselor/common/counselor.head.php`
- `pages/counselor/common/counselor.html.head.php`
- `pages/counselor/common/counselor.footer.php`
- `public/assets/css/counselor/base.css`

Batch 2:

- `pages/counselor/sessions/index.php`
- `pages/counselor/sessions/sessions.controller.php`
- `pages/counselor/sessions/sessions.layout.php`
- `public/assets/js/counselor/sessions.js`
- `public/assets/js/counselor/followUp.js`
- `public/assets/js/counselor/rescheduleRequests.js`

Batch 3:

- `pages/counselor/recovery-plans/create/index.php`
- `pages/counselor/recovery-plans/create/create.controller.php`
- `pages/counselor/recovery-plans/create/create.layout.php`
- `public/assets/js/counselor/createRecoveryPlan.js`
- `pages/counselor/recovery-plans/view/index.php`
- `pages/counselor/recovery-plans/view/view.layout.php`

### Admin Role Batches

Batch 1:

- `pages/admin/common/admin.head.php`
- `pages/admin/common/admin.html.head.php`
- `pages/admin/common/admin.footer.php`
- `pages/admin/common/admin.sidebar.php`

Batch 2:

- `pages/admin/resources/index.php`
- `pages/admin/resources/resources.controller.php`
- `pages/admin/resources/resources.layout.php`
- `pages/admin/help-center/add/index.php`
- `pages/admin/help-center/edit/index.php`
- `pages/admin/help-center/delete/index.php`

Batch 3:

- `pages/admin/job-posts/add/index.php`
- `pages/admin/job-posts/edit/index.php`
- `pages/admin/job-posts/delete/index.php`
- `pages/admin/content-management/index.php`
- `pages/admin/content-management/content-management.controller.php`
- `pages/admin/content-management/content-management.layout.php`

## Code Check Playbook

When you are asked to add another CRUD or change a feature:

1. Find the route entry file first.
2. Identify whether the change is GET, POST, or AJAX.
3. Find the controller or inline request logic that owns the write.
4. Find the layout and partials that render the UI.
5. Find CSS and JS loaded by that page.
6. Confirm redirect and flash message behavior after save/delete.
7. Change the smallest number of files possible.

Use this as the emergency checklist:

- What URL is being hit?
- Which `index.php` owns it?
- Which file writes to the database?
- Which file renders the HTML?
- Which JS file enhances the page?
- Which redirect shows success or error?

## Cleanup Challenge Board

These are good teaching challenges because they are real repo problems.

### Challenge 1: Normalize Profile Script Loading

Problem:

- `pages/user/profile/profile.layout.php` hardcodes scripts and re-loads Lucide directly in the layout.

Goal:

- move profile page scripts into `$pageScripts`
- rely on the shared footer instead of page-local script tags

What this teaches:

- page shell consistency
- avoiding duplicate third-party script loading

### Challenge 2: Move Inline Counselor Search JS Out Of The Layout

Problem:

- `pages/user/counselors/counselors.layout.php` contains inline JavaScript for filter toggling and search behavior.

Goal:

- move that logic into a dedicated feature JS file
- keep layout PHP focused on markup

What this teaches:

- separation of concerns
- keeping layouts readable

### Challenge 3: Split Follow-Up Into Proper Files

Problem:

- `pages/user/sessions/follow-up/index.php` mixes route logic, DB writes, HTML, and inline browser JavaScript in one file.

Goal:

- split it into controller plus layout
- move browser logic to a dedicated JS file

What this teaches:

- how to untangle a page without changing the route
- how to preserve behavior while cleaning structure

### Challenge 4: Fix Dead Or Mismatched Dashboard Behavior

Problem:

- `public/assets/js/user/dashboard.js` expects behavior and endpoints that do not line up cleanly with the current dashboard layout
- it references `/user/dashboard/save-task`
- it looks for `.quick-log-content form` even though the form itself already has that class

Goal:

- verify which dashboard interactions are real
- remove dead assumptions
- wire only the routes that actually exist

What this teaches:

- frontend to backend traceability
- how vibe-coded JS drifts away from PHP reality

### Challenge 5: Remove Placeholder Recovery Actions

Problem:

- `public/assets/js/user/recovery.js` contains placeholder notifications for actions that are not actually wired in PHP.

Goal:

- replace placeholder interception with real route-aware behavior
- delete dead UI-only code where the PHP route already exists

What this teaches:

- not every button needs JavaScript
- PHP form and route ownership matters

### Challenge 6: Remove Cross-Role CSS Coupling

Problem:

- `public/assets/css/counselor/base.css` imports `../user/dashboard.css`

Goal:

- create a true shared base or move shared primitives into a neutral layer
- stop counselor styles from depending on user page styles

What this teaches:

- why CSS ownership matters
- how style reuse can become architecture drift

## Recommended Team Outputs

By the end of this study plan, your team should produce:

1. A one-page route map for user, counselor, and admin.
2. A CRUD template showing where add, edit, delete, list, and redirect files live.
3. A list of known messy files and why they are risky.
4. A small challenge backlog with owners.
5. A short teaching demo where each member explains one feature end to end.

## Recommended Teaching Format

Use this format for each team session:

1. One person traces the route.
2. One person traces the write path.
3. One person traces CSS and JS loading.
4. One person identifies messy or risky code.
5. End with one small challenge or change.

If the team gets lost, go back to the route entry file and rebuild the flow from there.
