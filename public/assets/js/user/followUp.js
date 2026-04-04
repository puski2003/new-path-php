document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn    = document.getElementById('followupToggleBtn');
    const popup        = document.getElementById('followupPopup');
    const closeBtn     = document.getElementById('followupCloseBtn');
    const sessionList  = document.getElementById('followupSessionList');
    const threadView   = document.getElementById('followupThreadView');
    const backBtn      = document.getElementById('followupBackBtn');
    const messagesEl   = document.getElementById('followupMessages');
    const compose      = document.getElementById('followupCompose');
    const threadAvatar = document.getElementById('followupThreadAvatar');
    const threadName   = document.getElementById('followupThreadName');
    const threadMeta   = document.getElementById('followupThreadMeta');

    if (!toggleBtn || !popup) return;

    let currentSessionId = null;
    let isLocked         = false;
    let msgCount         = 0;
    let daysLeft         = 0;

    /* ── Toggle popup ─────────────────────────────────────── */
    toggleBtn.addEventListener('click', function () {
        popup.classList.toggle('active');
        if (popup.classList.contains('active')) lucide.createIcons();
    });

    closeBtn.addEventListener('click', function () {
        popup.classList.remove('active');
        closeThread();
    });

    document.addEventListener('click', function (e) {
        if (popup && toggleBtn &&
            !popup.contains(e.target) &&
            !toggleBtn.contains(e.target)) {
            popup.classList.remove('active');
            closeThread();
        }
    });

    /* ── Open thread on session item click ────────────────── */
    if (sessionList) {
        sessionList.addEventListener('click', function (e) {
            const item = e.target.closest('.fu-session-item');
            if (!item) return;

            currentSessionId = parseInt(item.dataset.sessionId, 10);
            const name   = item.dataset.name   || 'Counselor';
            const avatar = item.dataset.avatar  || '/assets/img/avatar.png';
            const date   = item.dataset.date    || '';

            threadAvatar.src            = avatar;
            threadName.textContent      = name;
            threadMeta.textContent      = date;
            messagesEl.innerHTML        = '<div class="fu-loading">Loading…</div>';

            threadView.classList.add('active');
            lucide.createIcons();

            loadMessages(currentSessionId);
        });
    }

    /* ── Back to list ─────────────────────────────────────── */
    if (backBtn) {
        backBtn.addEventListener('click', closeThread);
    }

    function closeThread() {
        threadView.classList.remove('active');
        currentSessionId = null;
    }

    /* ── Load messages via AJAX ───────────────────────────── */
    function loadMessages(sessionId) {
        fetch('/user/sessions?ajax=get_messages&session_id=' + sessionId)
            .then(r => r.json())
            .then(function (data) {
                if (!data.success) {
                    messagesEl.innerHTML = '<div class="fu-empty"><p>Could not load messages.</p></div>';
                    return;
                }

                isLocked = data.isLocked;
                msgCount = data.msgCount;
                daysLeft = data.daysLeft;

                renderMessages(data.messages);
                updateCompose();
            })
            .catch(function () {
                messagesEl.innerHTML = '<div class="fu-empty"><p>Could not load messages.</p></div>';
            });
    }

    function renderMessages(messages) {
        if (!messages.length) {
            messagesEl.innerHTML = '<div class="fu-empty"><i data-lucide="message-circle" stroke-width="1.5"></i><p>No messages yet. Start the conversation!</p></div>';
            lucide.createIcons();
            return;
        }

        messagesEl.innerHTML = messages.map(function (m) {
            const cls    = m.isMe ? 'fu-msg mine' : 'fu-msg';
            const avatar = escapeHtml(m.avatar);
            const name   = escapeHtml(m.name);
            const text   = escapeHtml(m.message).replace(/\n/g, '<br>');
            const time   = escapeHtml(m.time);
            return `<div class="${cls}">
                <img class="fu-msg-avatar" src="${avatar}" alt="" />
                <div class="fu-msg-wrap">
                    <span class="fu-msg-sender">${name}</span>
                    <div class="fu-msg-bubble">${text}</div>
                    <span class="fu-msg-time">${time}</span>
                </div>
            </div>`;
        }).join('');

        scrollToBottom();
        lucide.createIcons();
    }

    function appendMessage(msg) {
        const cls    = msg.isMe ? 'fu-msg mine' : 'fu-msg';
        const avatar = escapeHtml(msg.avatar);
        const name   = escapeHtml(msg.name);
        const text   = escapeHtml(msg.message).replace(/\n/g, '<br>');
        const time   = escapeHtml(msg.time);
        const el     = document.createElement('div');
        el.className = cls;
        el.innerHTML = `<img class="fu-msg-avatar" src="${avatar}" alt="" />
            <div class="fu-msg-wrap">
                <span class="fu-msg-sender">${name}</span>
                <div class="fu-msg-bubble">${text}</div>
                <span class="fu-msg-time">${time}</span>
            </div>`;

        const empty = messagesEl.querySelector('.fu-empty');
        if (empty) empty.remove();

        messagesEl.appendChild(el);
        scrollToBottom();
    }

    function updateCompose() {
        const metaText = isLocked
            ? 'Thread closed'
            : msgCount + '/5 messages · ' + daysLeft + ' day' + (daysLeft !== 1 ? 's' : '') + ' left';
        threadMeta.textContent = metaText;

        if (isLocked) {
            compose.outerHTML = '<div id="followupCompose" class="fu-locked-notice"><p><i data-lucide="lock" style="width:12px;height:12px;vertical-align:middle;"></i> Thread closed</p></div>';
            lucide.createIcons();
        } else {
            bindCompose();
        }
    }

    /* ── Send message ─────────────────────────────────────── */
    function bindCompose() {
        const inp  = compose ? compose.querySelector('input') : null;
        const sBtn = compose ? compose.querySelector('.fu-send-btn') : null;
        if (!inp || !sBtn) return;

        sBtn.onclick = doSend;
        inp.onkeydown = function (e) {
            if (e.key === 'Enter') { e.preventDefault(); doSend(); }
        };
    }

    function doSend() {
        const inp = compose ? compose.querySelector('input') : null;
        if (!inp) return;
        const text = inp.value.trim();
        if (!text || isLocked || !currentSessionId) return;

        const sBtn = compose.querySelector('.fu-send-btn');
        if (sBtn) sBtn.disabled = true;

        const fd = new FormData();
        fd.append('session_id', currentSessionId);
        fd.append('message', text);

        fetch('/user/sessions?ajax=send_message', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(function (data) {
                if (data.success) {
                    inp.value = '';
                    appendMessage(data.message);
                    msgCount  = data.msgCount;
                    daysLeft  = data.daysLeft;
                    isLocked  = msgCount >= 5;
                    if (isLocked) updateCompose();
                    else threadMeta.textContent = msgCount + '/5 messages · ' + daysLeft + ' day' + (daysLeft !== 1 ? 's' : '') + ' left';
                }
                if (sBtn) sBtn.disabled = false;
            })
            .catch(function () {
                if (sBtn) sBtn.disabled = false;
            });
    }

    bindCompose();

    /* ── Helpers ──────────────────────────────────────────── */
    function scrollToBottom() {
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }
});
