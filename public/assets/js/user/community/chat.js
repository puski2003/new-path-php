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
    let dmPollInterval = null;
    let groupPollInterval = null;

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
        currentDmConversationId = conversationId;

        document.getElementById('dmConversationName').textContent = userName;
        document.getElementById('dmConversationAvatar').src = profilePicture || '/assets/img/avatar.png';

        document.getElementById('directTab').style.display = 'none';
        document.getElementById('supportTab').style.display = 'none';
        dmConversationView.classList.add('active');

        loadDmMessages(conversationId);

        dmPollInterval = setInterval(() => {
            loadDmMessages(conversationId, true);
        }, 5000);

        lucide.createIcons();
    }

    function closeDmConversation() {
        currentDmConversationId = null;
        if (dmPollInterval) {
            clearInterval(dmPollInterval);
            dmPollInterval = null;
        }

        dmConversationView.classList.remove('active');
        const activeTab = document.querySelector('.chat-tab--active');
        const tabName = activeTab ? activeTab.dataset.tab : 'direct';

        if (tabName === 'direct') {
            document.getElementById('directTab').style.display = 'flex';
        } else {
            document.getElementById('supportTab').style.display = 'flex';
        }

        refreshChatData();
    }

    function openGroupConversation(groupId, groupName) {
        currentGroupId = groupId;

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

        groupPollInterval = setInterval(() => {
            loadGroupMessages(groupId, true);
        }, 5000);

        lucide.createIcons();
    }

    function closeGroupConversation() {
        currentGroupId = null;
        if (groupPollInterval) {
            clearInterval(groupPollInterval);
            groupPollInterval = null;
        }

        groupConversationView.classList.remove('active');
        const activeTab = document.querySelector('.chat-tab--active');
        const tabName = activeTab ? activeTab.dataset.tab : 'direct';

        if (tabName === 'direct') {
            document.getElementById('directTab').style.display = 'flex';
        } else {
            document.getElementById('supportTab').style.display = 'flex';
        }

        refreshChatData();
    }

    function loadDmMessages(conversationId, silent = false) {
        fetch(`/user/community?ajax=get_dm_messages&conversation_id=${conversationId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderDmMessages(data.html, silent);
                }
            })
            .catch(error => console.error('Error loading DM messages:', error));
    }

    function renderDmMessages(html, silent = false) {
        const container = document.getElementById('dmMessagesContainer');
        if (!container) return;

        container.innerHTML = html || '';
        lucide.createIcons();
        container.scrollTop = container.scrollHeight;
    }

    function loadGroupMessages(groupId, silent = false) {
        fetch(`/user/community?ajax=get_group_messages&group_id=${groupId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderGroupMessages(data.html, data.group);
                }
            })
            .catch(error => console.error('Error loading group messages:', error));
    }

    function renderGroupMessages(html, group) {
        const container = document.getElementById('groupMessagesContainer');
        if (!container) return;

        container.innerHTML = html || '';
        lucide.createIcons();
        container.scrollTop = container.scrollHeight;

        if (group) {
            document.getElementById('groupConversationStatus').textContent = `${group.memberCount} members`;
            document.getElementById('groupMemberCount').textContent = group.memberCount;

            const infoPanel = document.getElementById('groupInfoPanel');
            const description = document.getElementById('groupDescription');
            const schedule = document.getElementById('groupMeetingSchedule');
            const hasDescription = Boolean(group.description);
            const hasSchedule = Boolean(group.meetingSchedule);

            if (description) {
                description.textContent = group.description || '';
            }
            if (schedule) {
                schedule.textContent = group.meetingSchedule || '';
            }
            if (infoPanel) {
                infoPanel.style.display = (hasDescription || hasSchedule) ? 'block' : 'none';
            }
        }
    }

    function sendDmMessage() {
        const input = document.getElementById('dmMessageInput');
        const content = input.value.trim();

        if (!content || !currentDmConversationId) return;

        const formData = new FormData();
        formData.append('conversation_id', currentDmConversationId);
        formData.append('content', content);

        fetch('/user/community?ajax=send_dm_message', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                input.value = '';
                loadDmMessages(currentDmConversationId, true);
            }
        })
        .catch(error => console.error('Error sending DM:', error));
    }

    function sendGroupMessage() {
        const input = document.getElementById('groupMessageInput');
        const content = input.value.trim();

        if (!content || !currentGroupId) return;

        const formData = new FormData();
        formData.append('group_id', currentGroupId);
        formData.append('content', content);

        fetch('/user/community?ajax=send_group_message', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                input.value = '';
                loadGroupMessages(currentGroupId, true);
            }
        })
        .catch(error => console.error('Error sending group message:', error));
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
                    setTimeout(() => item.remove(), 300);
                }
                refreshChatData();
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

    function refreshChatData() {
        fetch('/user/community?ajax=get_dm_messages&conversation_id=0')
            .catch(() => {});

        location.reload();
    }

    function stopPolling() {
        if (dmPollInterval) {
            clearInterval(dmPollInterval);
            dmPollInterval = null;
        }
        if (groupPollInterval) {
            clearInterval(groupPollInterval);
            groupPollInterval = null;
        }
    }

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
