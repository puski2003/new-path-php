# JS to PHP Refactor Plan

## Goal

Reduce server-renderable markup built in JavaScript and move persistent UI/content rendering into PHP partials and layouts.

Keep JavaScript focused on:

- event binding
- open/close state
- form submission
- polling
- class toggles
- swapping server-rendered HTML fragments into containers

Do not use this refactor to remove all DOM APIs. The target is to remove page/content template construction from JS, not basic UI behavior.

## Verified Problem Areas

### Priority 1: Community chat message rendering

Current issue:

- `public/assets/js/user/chat.js` builds direct-message and group-message markup with `innerHTML`.
- The chat shell already exists in PHP at `pages/user/common/user.community-chat.php`.
- The controller currently returns JSON message data and forces the frontend to assemble HTML.

Files involved:

- `public/assets/js/user/chat.js`
- `pages/user/community/community.controller.php`
- `pages/user/common/user.community-chat.php`
- `pages/user/common/direct-message.model.php`
- `pages/user/common/support-group.model.php`

Refactor target:

- Create PHP partials for message rows.
- Return server-rendered HTML fragments for message lists.
- Keep JS responsible only for:
  - opening conversations
  - polling
  - sending messages
  - replacing container contents with returned HTML
  - scroll positioning

New partials to add:

- `pages/user/common/user.chat-dm-message-item.php`
- `pages/user/common/user.chat-group-message-item.php`
- `pages/user/common/user.chat-empty-state.php`

Controller changes:

- Extend `get_dm_messages` to optionally return:
  - `html`
  - `hasMessages`
- Extend `get_group_messages` to optionally return:
  - `html`
  - `hasMessages`
  - existing group metadata
- Use output buffering and `require` to render partials.

JavaScript changes:

- Remove message template strings from:
  - `renderDmMessages()`
  - `renderGroupMessages()`
- Replace them with logic that injects returned `html`.
- Keep `formatTime()` only if the server does not format timestamps. Prefer formatting in PHP for consistency with initial render.

Acceptance criteria:

- No message row HTML is constructed in `chat.js`.
- Empty states for chats are rendered by PHP partials.
- Polling still works.
- Send message still updates the thread correctly.

## Priority 2: Community post card rendering

Current issue:

- `public/assets/js/user/community.js` contains `createPostElement()` which builds a full post card in JS.
- The same UI already exists in `pages/user/common/user.community-post-item.php`.
- This creates duplicate markup rules between PHP and JS.

Files involved:

- `public/assets/js/user/community.js`
- `pages/user/common/user.community-post-item.php`
- `pages/user/common/user.community-post-actions.php`
- `pages/user/community/community.layout.php`
- `pages/user/community/community.controller.php`
- `pages/user/community/community.model.php`

Refactor target:

- Make `user.community-post-item.php` the single source of truth for post markup.
- Remove or retire `createPostElement()` from `community.js`.
- If async post loading or filtering is introduced, return rendered PHP fragments instead of raw JSON records for display markup.

Server-side rendering pattern:

- Fetch posts in controller/model
- Loop through posts in PHP
- `require` `user.community-post-item.php`
- Return fragment HTML for AJAX views when needed

JavaScript changes:

- Keep:
  - modal open/close logic
  - edit/delete menu handling
  - like button request handling
  - follow/unfollow button state updates
- Remove:
  - any full post-card template construction
  - empty-state HTML template strings for posts when server can render them

Acceptance criteria:

- Only PHP partials render post card markup.
- JS no longer contains a full post card template.
- Existing like/edit/delete behavior still works.

## Priority 3: Help center service cards and details

Current issue:

- `public/assets/js/user/helpCenter.js` renders the support-service card grid from `window.supportServices`.
- It also renders the details modal body with a large template string.
- These are page-content components, not transient JS-only widgets.

Files involved:

- `public/assets/js/user/helpCenter.js`
- `pages/user/help/help.layout.php`
- `pages/user/help/help.controller.php`
- `pages/user/help/help.model.php`

Refactor target:

- Render service cards in PHP on initial page load.
- Render service details in PHP as either:
  - hidden fragments per service, or
  - an HTML fragment returned by a controller endpoint
- Keep JS for:
  - search input listeners
  - filter interactions
  - pagination events
  - opening/closing the modal

Recommended implementation:

- Add partials:
  - `pages/user/common/user.help-service-card.php`
  - `pages/user/common/user.help-service-empty-state.php`
  - `pages/user/common/user.help-service-detail.php`
- Replace `window.supportServices` as the primary rendering source.
- If client-side filtering must stay, use pre-rendered data attributes on existing cards for filtering instead of rebuilding markup.

Preferred filtering approach:

- Short term: use server-rendered cards plus client-side show/hide and server-rendered pagination fragments if needed.
- Long term: move search/filter state to query params and let PHP render the result set.

Acceptance criteria:

- No service-card template string remains in `helpCenter.js`.
- No service-detail modal body is assembled in JS.
- Search/filter/pagination still work.

## Priority 4: Small user-area button/menu cleanup

These are lower risk and can be handled after the main content refactors.

Files:

- `public/assets/js/user/find-people.js`
- `public/assets/js/user/profile.js`
- `public/assets/js/user/community.js`

Current issue:

- Some buttons are re-rendered with `innerHTML` only to swap icon + label.
- Small menus are assembled as HTML strings.

Refactor target:

- Prefer static button markup with child elements already present in PHP.
- Toggle text/classes/attributes instead of replacing full button HTML.
- For menus, prefer hidden server-rendered menu containers when practical.

Acceptance criteria:

- Follow buttons update without replacing their full inner HTML.
- Small dropdown/menu markup is pre-rendered where reasonable.

## Priority 5: Optional cleanup, not the first wave

These use dynamic DOM creation, but they are not as strong a server-rendering candidate because they are transient or form-driven.

Files:

- `public/assets/js/user/recovery.js`
- `public/assets/js/counselor/createRecoveryPlan.js`
- `public/assets/js/user/dashboard.js`

Guidance:

- Notifications and temporary modals can remain in JS.
- Dynamic repeatable form rows can remain in JS unless the team adopts a stricter component strategy.
- Inline template strings should still be reduced over time, but these are not the first priority.

## Refactor Rules

Use these rules throughout the work:

1. PHP partials are the source of truth for persistent markup.
2. Controllers may return HTML fragments for AJAX requests.
3. Avoid duplicating the same card/row structure in both PHP and JS.
4. Prefer `data-*` attributes for JS state hooks instead of rebuilding nodes.
5. Use JS to replace container contents with server-rendered HTML, not to compose the HTML.
6. Keep icon refresh calls only where DOM replacement still happens.

## Recommended Execution Order

1. Refactor chat message rendering
2. Remove JS post-card rendering and standardize post partial reuse
3. Refactor help center card/detail rendering
4. Clean up follow-button/menu patterns
5. Revisit optional transient UI templates

## Implementation Checklist

### Phase 1: Chat

- Add `user.chat-dm-message-item.php`
- Add `user.chat-group-message-item.php`
- Add `user.chat-empty-state.php`
- Update `community.controller.php` fragment responses for chat
- Rewrite `chat.js` render functions to inject server HTML only
- Verify DM and group message polling
- Verify empty states and pinned/group role states

### Phase 2: Community posts

- Confirm all post markup variants live in `user.community-post-item.php`
- Remove `createPostElement()` from `community.js`
- Remove JS-generated post empty state if server-rendered
- Add fragment endpoint only if async post loading is required
- Verify owner menu, edit, delete, like, and follow behaviors

### Phase 3: Help center

- Add service-card partial
- Add detail partial
- Render initial cards in `help.layout.php`
- Replace JS card rendering with filtering/show-hide or HTML fragment swapping
- Replace JS modal-content template with server-rendered details
- Verify search, filter, pagination, and contact actions

### Phase 4: Small cleanup

- Replace follow-button `innerHTML` swaps with text/class toggles
- Replace ad hoc menu template strings with pre-rendered hidden menus where useful
- Re-run icon initialization only where needed

## Verification Pass

After each phase:

- Run a targeted search for `innerHTML|insertAdjacentHTML` in edited files
- Confirm the remaining occurrences are only:
  - clearing container contents
  - icon-only swaps
  - transient notifications/modals still intentionally kept in JS
- Manually verify the affected page interactions

Suggested search:

```powershell
rg -n "innerHTML|insertAdjacentHTML" public/assets/js/user pages/user
```

## Expected End State

- PHP renders post cards, chat message rows, help-center cards, and help-center details.
- JS orchestrates behavior but no longer owns page-content templates.
- Initial render and AJAX-updated UI use the same partials, reducing drift and maintenance cost.
