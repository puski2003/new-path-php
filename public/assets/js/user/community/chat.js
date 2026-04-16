document.addEventListener('DOMContentLoaded', function() {
    const chatToggleBtn = document.getElementById('chatToggleBtn');
    const chatPopup = document.getElementById('chatPopup');
    const chatCloseBtn = document.getElementById('chatCloseBtn');
    const chatTabs = document.querySelectorAll('.chat-tab');
    const chatTabContents = document.querySelectorAll('.chat-tab-content');

    const dmConversationView = document.getElementById('dmConversationView');
    const groupConversationView = document.getElementById('groupConversationView');
    const dmBackBtn = document.getElementById('dmBackBtn');
    const groupBackBtn = document.getElementById('groupBackBtn');

    let currentDmConversationId = null;
    let currentGroupId = null;
    let lastDmMsgId = 0;
    let lastGroupMsgId = 0;
    let firstDmMsgId = 0;
    let firstGroupMsgId = 0;

    // ── Relative time formatting ──────────────────────────────────
    function formatRelativeTime(isoString) {
        if (!isoString) return '';
        const date = new Date(isoString);
        if (isNaN(date)) return '';
        const now = new Date();
        const diffSec = Math.floor((now - date) / 1000);
        const diffMin = Math.floor(diffSec / 60);
        const diffHr  = Math.floor(diffMin / 60);
        if (diffSec < 60)  return 'just now';
        if (diffMin < 60)  return diffMin + 'm ago';
        if (diffHr  < 24)  return diffHr  + 'h ago';
        const opts = { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' };
        if (date.getFullYear() !== now.getFullYear()) opts.year = 'numeric';
        return date.toLocaleString('en-US', opts);
    }

    function updateMessageTimes() {
        document.querySelectorAll('.message-time[data-time]').forEach(function(el) {
            const t = el.getAttribute('data-time');
            if (t) el.textContent = formatRelativeTime(t);
        });
    }

    setInterval(updateMessageTimes, 60000);
    // ─────────────────────────────────────────────────────────────

    // ── Load-earlier button helpers ───────────────────────────────
    function showLoadEarlierBtn(containerId, firstMsgId, type) {
        const container = document.getElementById(containerId);
        if (!container) return;
        let btn = container.querySelector('.load-earlier-btn');
        if (!btn) {
            btn = document.createElement('button');
            btn.className = 'load-earlier-btn';
            btn.dataset.type = type;
            container.insertBefore(btn, container.firstChild);
        }
        btn.dataset.firstId = firstMsgId;
        btn.disabled = false;
        btn.textContent = 'Load earlier messages';
    }

    function hideLoadEarlierBtn(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;
        const btn = container.querySelector('.load-earlier-btn');
        if (btn) btn.remove();
    }

    function prependMessages(containerId, html) {
        const container = document.getElementById(containerId);
        if (!container || !html) return;
        const prevScrollHeight = container.scrollHeight;
        const btn = container.querySelector('.load-earlier-btn');
        if (btn) {
            btn.insertAdjacentHTML('afterend', html);
        } else {
            container.insertAdjacentHTML('afterbegin', html);
        }
        lucide.createIcons();
        updateMessageTimes();
        container.scrollTop += container.scrollHeight - prevScrollHeight;
    }
    // ─────────────────────────────────────────────────────────────

    // ── Centralized poller ────────────────────────────────────────
    // Only one conversation is open at a time, so a single timer covers both.
    const poll = {
        task: null,
        containerId: null,
        lastMsgId: 0,
        urlFn: null
    };

    function startPolling(urlFn, containerId, initialLastMsgId) {
        stopPolling();
        poll.urlFn       = urlFn;
        poll.containerId = containerId;
        poll.lastMsgId   = initialLastMsgId || 0;
        poll.task = window.NewPathPolling.createTask({
            interval: 4000,
            runImmediately: false,
            request: function () {
                return fetch(poll.urlFn(poll.lastMsgId))
                    .then(function (r) { return r.json(); });
            },
            onSuccess: function (data) {
                if (!data || !data.success || !data.html) return;
                appendMessages(poll.containerId, data.html);
                poll.lastMsgId = data.lastMsgId || poll.lastMsgId;
                if (poll.containerId === 'dmMessagesContainer') {
                    lastDmMsgId = poll.lastMsgId;
                } else if (poll.containerId === 'groupMessagesContainer') {
                    lastGroupMsgId = poll.lastMsgId;
                }
            }
        });
        poll.task.start();
    }

    function stopPolling() {
        if (poll.task) {
            poll.task.stop();
            poll.task = null;
        }
        poll.lastMsgId   = 0;
        poll.containerId = null;
        poll.urlFn       = null;
    }

    function pollNow() {
        if (!poll.urlFn || !poll.containerId) return Promise.resolve();
        if (poll.task) {
            return poll.task.runNow();
        }
        return Promise.resolve();
    }
    // ─────────────────────────────────────────────────────────────

    if (chatToggleBtn) {
        chatToggleBtn.addEventListener('click', function() {
            if (chatPopup) {
                chatPopup.classList.toggle('active');
                if (chatPopup.classList.contains('active')) {
                    lucide.createIcons();
                }
            }
        });
    }

    if (chatCloseBtn) {
        chatCloseBtn.addEventListener('click', function() {
            if (chatPopup) {
                chatPopup.classList.remove('active');
                stopPolling();
            }
        });
    }

    document.addEventListener('click', function(event) {
        if (chatPopup && chatToggleBtn &&
            !chatPopup.contains(event.target) &&
            !chatToggleBtn.contains(event.target)) {
            chatPopup.classList.remove('active');
            stopPolling();
        }
    });

    chatTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');

            chatTabs.forEach(t => t.classList.remove('chat-tab--active'));
            chatTabContents.forEach(content => {
                content.classList.remove('chat-tab-content--active');
                content.style.display = 'none';
            });

            this.classList.add('chat-tab--active');

            if (targetTab === 'direct') {
                const directTab = document.getElementById('directTab');
                if (directTab) {
                    directTab.classList.add('chat-tab-content--active');
                    directTab.style.display = 'flex';
                }
            } else if (targetTab === 'support') {
                const supportTab = document.getElementById('supportTab');
                if (supportTab) {
                    supportTab.classList.add('chat-tab-content--active');
                    supportTab.style.display = 'flex';
                }
            }

            lucide.createIcons();
        });
    });

    const dmChatList = document.getElementById('directTab');
    if (dmChatList) {
        dmChatList.addEventListener('click', function(e) {
            const conversationItem = e.target.closest('.dm-conversation-item');
            if (conversationItem) {
                const conversationId = conversationItem.dataset.conversationId;
                const userName = conversationItem.dataset.userName;
                const profilePicture = conversationItem.dataset.profilePicture;
                openDmConversation(conversationId, userName, profilePicture);
            }
        });
    }

    const supportChatList = document.getElementById('supportTab');
    if (supportChatList) {
        supportChatList.addEventListener('click', function(e) {
            const groupItem = e.target.closest('.support-group-item');
            if (groupItem) {
                const groupId = groupItem.dataset.groupId;
                const groupName = groupItem.dataset.groupName;
                openGroupConversation(groupId, groupName);
            }
        });
    }

    if (dmBackBtn) {
        dmBackBtn.addEventListener('click', function() {
            closeDmConversation();
        });
    }

    if (groupBackBtn) {
        groupBackBtn.addEventListener('click', function() {
            closeGroupConversation();
        });
    }

    const dmSendBtn = document.getElementById('dmSendBtn');
    const dmMessageInput = document.getElementById('dmMessageInput');
    if (dmSendBtn && dmMessageInput) {
        dmSendBtn.addEventListener('click', function() {
            sendDmMessage();
        });
        dmMessageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendDmMessage();
            }
        });
    }

    const groupSendBtn = document.getElementById('groupSendBtn');
    const groupMessageInput = document.getElementById('groupMessageInput');
    if (groupSendBtn && groupMessageInput) {
        groupSendBtn.addEventListener('click', function() {
            sendGroupMessage();
        });
        groupMessageInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendGroupMessage();
            }
        });
    }

    const dmAcceptBtns = document.querySelectorAll('.accept-btn');
    dmAcceptBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const connectionId = this.dataset.connectionId;
            handleAcceptConnection(connectionId, this);
        });
    });

    const dmDeclineBtns = document.querySelectorAll('.decline-btn');
    dmDeclineBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const connectionId = this.dataset.connectionId;
            handleDeclineConnection(connectionId, this);
        });
    });

    function openDmConversation(conversationId, userName, profilePicture) {
        stopPolling();
        currentDmConversationId = conversationId;
        lastDmMsgId  = 0;
        firstDmMsgId = 0;

        document.getElementById('dmConversationName').textContent = userName;
        document.getElementById('dmConversationAvatar').src = profilePicture || '/assets/img/avatar.png';

        document.getElementById('directTab').style.display = 'none';
        document.getElementById('supportTab').style.display = 'none';
        dmConversationView.classList.add('active');

        loadDmMessages(conversationId);
        startPolling(function (lastMsgId) {
            return '/user/community?ajax=poll_dm_messages&conversation_id=' + currentDmConversationId + '&last_id=' + lastMsgId;
        }, 'dmMessagesContainer', lastDmMsgId);

        lucide.createIcons();
    }

    function closeDmConversation() {
        stopPolling();
        currentDmConversationId = null;
        lastDmMsgId  = 0;
        firstDmMsgId = 0;

        dmConversationView.classList.remove('active');
        const activeTab = document.querySelector('.chat-tab--active');
        const tabName = activeTab ? activeTab.dataset.tab : 'direct';

        if (tabName === 'direct') {
            document.getElementById('directTab').style.display = 'flex';
        } else {
            document.getElementById('supportTab').style.display = 'flex';
        }
    }

    function openGroupConversation(groupId, groupName) {
        stopPolling();
        currentGroupId  = groupId;
        lastGroupMsgId  = 0;
        firstGroupMsgId = 0;

        document.getElementById('groupConversationName').textContent = groupName;

        const groupItem = document.querySelector(`.support-group-item[data-group-id="${groupId}"]`);
        const meetingLink = groupItem ? groupItem.dataset.meetingLink : '';
        const meetingBtn = document.getElementById('groupMeetingBtn');

        if (meetingLink) {
            meetingBtn.style.display = 'block';
            meetingBtn.onclick = function() {
                window.open(meetingLink, '_blank');
            };
        } else {
            meetingBtn.style.display = 'none';
        }

        document.getElementById('directTab').style.display = 'none';
        document.getElementById('supportTab').style.display = 'none';
        groupConversationView.classList.add('active');

        loadGroupMessages(groupId);
        startPolling(function (lastMsgId) {
            return '/user/community?ajax=poll_group_messages&group_id=' + currentGroupId + '&last_id=' + lastMsgId;
        }, 'groupMessagesContainer', lastGroupMsgId);

        lucide.createIcons();
    }

    function closeGroupConversation() {
        stopPolling();
        currentGroupId  = null;
        lastGroupMsgId  = 0;
        firstGroupMsgId = 0;

        groupConversationView.classList.remove('active');
        const activeTab = document.querySelector('.chat-tab--active');
        const tabName = activeTab ? activeTab.dataset.tab : 'direct';

        if (tabName === 'direct') {
            document.getElementById('directTab').style.display = 'flex';
        } else {
            document.getElementById('supportTab').style.display = 'flex';
        }
    }

    function appendMessages(containerId, html) {
        const container = document.getElementById(containerId);
        if (!container || !html) return;
        const atBottom = container.scrollHeight - container.scrollTop - container.clientHeight < 60;
        container.insertAdjacentHTML('beforeend', html);
        lucide.createIcons();
        updateMessageTimes();
        if (atBottom) container.scrollTop = container.scrollHeight;
    }

    function loadDmMessages(conversationId) {
        fetch(`/user/community?ajax=get_dm_messages&conversation_id=${conversationId}`)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data.success) return;
                const container = document.getElementById('dmMessagesContainer');
                if (container) {
                    container.innerHTML = data.html || '';
                    lucide.createIcons();
                    updateMessageTimes();
                    container.scrollTop = container.scrollHeight;
                }
                lastDmMsgId  = data.lastMsgId  || 0;
                firstDmMsgId = data.firstMsgId || 0;
                if (currentDmConversationId === conversationId) {
                    poll.lastMsgId = lastDmMsgId;
                }
                if (data.hasMore && firstDmMsgId > 0) {
                    showLoadEarlierBtn('dmMessagesContainer', firstDmMsgId, 'dm');
                } else {
                    hideLoadEarlierBtn('dmMessagesContainer');
                }
            })
            .catch(function(e) { console.error('Error loading DM messages:', e); });
    }

    function loadOlderDmMessages(beforeId) {
        fetch('/user/community?ajax=load_older_dm_messages&conversation_id=' + currentDmConversationId + '&before_id=' + beforeId)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data.success) { hideLoadEarlierBtn('dmMessagesContainer'); return; }
                if (data.html) {
                    prependMessages('dmMessagesContainer', data.html);
                    firstDmMsgId = data.firstMsgId || firstDmMsgId;
                }
                if (data.hasMore && firstDmMsgId > 0) {
                    showLoadEarlierBtn('dmMessagesContainer', firstDmMsgId, 'dm');
                } else {
                    hideLoadEarlierBtn('dmMessagesContainer');
                }
            })
            .catch(function(e) { console.error('Error loading older DM messages:', e); hideLoadEarlierBtn('dmMessagesContainer'); });
    }

    function loadGroupMessages(groupId) {
        fetch(`/user/community?ajax=get_group_messages&group_id=${groupId}`)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data.success) return;
                const container = document.getElementById('groupMessagesContainer');
                if (container) {
                    container.innerHTML = data.html || '';
                    lucide.createIcons();
                    updateMessageTimes();
                    container.scrollTop = container.scrollHeight;
                }
                lastGroupMsgId  = data.lastMsgId  || 0;
                firstGroupMsgId = data.firstMsgId || 0;
                if (currentGroupId === groupId) {
                    poll.lastMsgId = lastGroupMsgId;
                }
                if (data.hasMore && firstGroupMsgId > 0) {
                    showLoadEarlierBtn('groupMessagesContainer', firstGroupMsgId, 'group');
                } else {
                    hideLoadEarlierBtn('groupMessagesContainer');
                }

                const group = data.group;
                if (group) {
                    const statusEl = document.getElementById('groupConversationStatus');
                    if (statusEl) statusEl.textContent = `${group.memberCount} members`;
                    const countEl = document.getElementById('groupMemberCount');
                    if (countEl) countEl.textContent = group.memberCount;
                    const infoPanel  = document.getElementById('groupInfoPanel');
                    const description = document.getElementById('groupDescription');
                    const schedule   = document.getElementById('groupMeetingSchedule');
                    if (description) description.textContent = group.description || '';
                    if (schedule)    schedule.textContent    = group.meetingSchedule || '';
                    if (infoPanel)   infoPanel.style.display = (group.description || group.meetingSchedule) ? 'block' : 'none';
                }
            })
            .catch(function(e) { console.error('Error loading group messages:', e); });
    }

    function loadOlderGroupMessages(beforeId) {
        fetch('/user/community?ajax=load_older_group_messages&group_id=' + currentGroupId + '&before_id=' + beforeId)
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (!data.success) { hideLoadEarlierBtn('groupMessagesContainer'); return; }
                if (data.html) {
                    prependMessages('groupMessagesContainer', data.html);
                    firstGroupMsgId = data.firstMsgId || firstGroupMsgId;
                }
                if (data.hasMore && firstGroupMsgId > 0) {
                    showLoadEarlierBtn('groupMessagesContainer', firstGroupMsgId, 'group');
                } else {
                    hideLoadEarlierBtn('groupMessagesContainer');
                }
            })
            .catch(function(e) { console.error('Error loading older group messages:', e); hideLoadEarlierBtn('groupMessagesContainer'); });
    }

    function sendDmMessage() {
        const input = document.getElementById('dmMessageInput');
        const btn   = document.getElementById('dmSendBtn');
        const content = input.value.trim();

        if (!content || !currentDmConversationId) return;

        input.value  = '';
        btn.disabled = true;

        const formData = new FormData();
        formData.append('conversation_id', currentDmConversationId);
        formData.append('content', content);

        fetch('/user/community?ajax=send_dm_message', {
            method: 'POST',
            body: formData
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (!data.success) { input.value = content; }
            btn.disabled = input.value.trim() === '';
            return pollNow().then(function () { lastDmMsgId = poll.lastMsgId; });
        })
        .catch(function(e) { console.error('Error sending DM:', e); input.value = content; btn.disabled = false; });
    }

    function sendGroupMessage() {
        const input = document.getElementById('groupMessageInput');
        const btn   = document.getElementById('groupSendBtn');
        const content = input.value.trim();

        if (!content || !currentGroupId) return;

        input.value  = '';
        btn.disabled = true;

        const formData = new FormData();
        formData.append('group_id', currentGroupId);
        formData.append('content', content);

        fetch('/user/community?ajax=send_group_message', {
            method: 'POST',
            body: formData
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (!data.success) { input.value = content; }
            btn.disabled = input.value.trim() === '';
            return pollNow().then(function () { lastGroupMsgId = poll.lastMsgId; });
        })
        .catch(function(e) { console.error('Error sending group message:', e); input.value = content; btn.disabled = false; });
    }

    function refreshChatData() {
        window.location.reload();
    }

    function handleAcceptConnection(connectionId, btn) {
        const formData = new FormData();
        formData.append('connection_id', connectionId);

        fetch('/user/community?ajax=accept_connection', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = btn.closest('.connection-request-item');
                if (item) {
                    item.style.transition = 'opacity 0.3s';
                    item.style.opacity = '0';
                    setTimeout(() => refreshChatData(), 350);
                } else {
                    refreshChatData();
                }
            }
        })
        .catch(error => console.error('Error accepting connection:', error));
    }

    function handleDeclineConnection(connectionId, btn) {
        const formData = new FormData();
        formData.append('connection_id', connectionId);

        fetch('/user/community?ajax=decline_connection', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = btn.closest('.connection-request-item');
                if (item) {
                    item.style.transition = 'opacity 0.3s';
                    item.style.opacity = '0';
                    setTimeout(() => item.remove(), 300);
                }
            }
        })
        .catch(error => console.error('Error declining connection:', error));
    }

    // ── Load-earlier button click (event delegation) ──────────────
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.load-earlier-btn');
        if (!btn || btn.disabled) return;
        btn.disabled = true;
        btn.textContent = 'Loading...';
        const firstId = parseInt(btn.dataset.firstId, 10);
        if (btn.dataset.type === 'dm' && currentDmConversationId) {
            loadOlderDmMessages(firstId);
        } else if (btn.dataset.type === 'group' && currentGroupId) {
            loadOlderGroupMessages(firstId);
        }
    });
    // ─────────────────────────────────────────────────────────────

    // ── Emoji picker ──────────────────────────────────────────────
    const EMOJIS = [
        '😀','😃','😄','😁','😆','😅','😂','🤣','😊','😇',
        '🙂','😉','😌','😍','🥰','😘','😋','😛','😜','🤪',
        '🤨','🧐','🤓','😎','🥳','😏','😒','😔','😟','🙁',
        '😣','😫','😩','🥺','😢','😭','😤','😠','😡','🤬',
        '😈','💀','💩','🤡','👻','😴','🥱','😷','🤒','🤕',
        '👍','👎','👏','🙌','🤝','🙏','✌️','🤞','👌','🤙',
        '❤️','🧡','💛','💚','💙','💜','🖤','💔','💕','💯',
        '🔥','⭐','✨','🎉','🎊','🎈','🎁','🏆','🥇','👑',
        '😸','😹','😺','🐶','🐱','🐭','🐹','🐰','🦊','🐻',
        '🍕','🍔','🍟','🌮','🍜','🍣','🍩','🍪','🎂','☕',
    ];

    let activeEmojiInput = null;

    const emojiPanel = (function () {
        const panel = document.createElement('div');
        panel.className = 'emoji-panel';
        panel.setAttribute('role', 'dialog');
        panel.setAttribute('aria-label', 'Emoji picker');

        const grid = document.createElement('div');
        grid.className = 'emoji-grid';

        EMOJIS.forEach(function (emoji) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'emoji-item';
            btn.textContent = emoji;
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                if (!activeEmojiInput) return;
                const start = activeEmojiInput.selectionStart;
                const end   = activeEmojiInput.selectionEnd;
                const val   = activeEmojiInput.value;
                activeEmojiInput.value = val.slice(0, start) + emoji + val.slice(end);
                activeEmojiInput.selectionStart = activeEmojiInput.selectionEnd = start + emoji.length;
                activeEmojiInput.dispatchEvent(new Event('input'));
                activeEmojiInput.focus();
                closeEmojiPanel();
            });
            grid.appendChild(btn);
        });

        panel.appendChild(grid);
        document.body.appendChild(panel);
        return panel;
    }());

    function openEmojiPanel(triggerBtn, targetInput) {
        activeEmojiInput = targetInput;
        const rect = triggerBtn.getBoundingClientRect();
        emojiPanel.style.bottom = (window.innerHeight - rect.top + 6) + 'px';
        emojiPanel.style.left   = Math.max(8, rect.left - 20) + 'px';
        emojiPanel.classList.add('active');
    }

    function closeEmojiPanel() {
        emojiPanel.classList.remove('active');
        activeEmojiInput = null;
    }

    function toggleEmojiPanel(triggerBtn, targetInput) {
        if (emojiPanel.classList.contains('active') && activeEmojiInput === targetInput) {
            closeEmojiPanel();
        } else {
            openEmojiPanel(triggerBtn, targetInput);
        }
    }

    const dmEmojiBtn    = document.getElementById('dmEmojiBtn');
    const groupEmojiBtn = document.getElementById('groupEmojiBtn');

    if (dmEmojiBtn && dmMessageInput) {
        dmEmojiBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            toggleEmojiPanel(dmEmojiBtn, dmMessageInput);
        });
    }
    if (groupEmojiBtn && groupMessageInput) {
        groupEmojiBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            toggleEmojiPanel(groupEmojiBtn, groupMessageInput);
        });
    }

    document.addEventListener('click', function (e) {
        if (emojiPanel.classList.contains('active') &&
            !emojiPanel.contains(e.target) &&
            e.target !== dmEmojiBtn &&
            e.target !== groupEmojiBtn) {
            closeEmojiPanel();
        }
    });
    // ─────────────────────────────────────────────────────────────

    // ── Send button: disable when input is empty ──────────────────
    function syncSendBtn(input, btn) {
        if (!input || !btn) return;
        btn.disabled = input.value.trim() === '';
        input.addEventListener('input', function() {
            btn.disabled = this.value.trim() === '';
        });
    }
    syncSendBtn(dmMessageInput, dmSendBtn);
    syncSendBtn(groupMessageInput, groupSendBtn);
    // ─────────────────────────────────────────────────────────────

    const searchInput = document.getElementById('chatSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const items = document.querySelectorAll('.chat-item');

            items.forEach(item => {
                const name = item.querySelector('.chat-name');
                if (name) {
                    const text = name.textContent.toLowerCase();
                    item.style.display = text.includes(query) ? 'flex' : 'none';
                }
            });
        });
    }
});
