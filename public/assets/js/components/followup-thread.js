(function () {
  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#39;");
  }

  function createMetaText(msgCount, daysLeft, isLocked) {
    if (isLocked) {
      return "Thread closed";
    }

    return (
      msgCount +
      "/5 messages · " +
      daysLeft +
      " day" +
      (daysLeft !== 1 ? "s" : "") +
      " left"
    );
  }

  function renderMessageBubble(message) {
    const className = message.isMe ? "fu-msg mine" : "fu-msg";
    return (
      '<div class="' +
      className +
      '">' +
      '<img class="fu-msg-avatar" src="' +
      escapeHtml(message.avatar) +
      '" alt="" />' +
      '<div class="fu-msg-wrap">' +
      '<span class="fu-msg-sender">' +
      escapeHtml(message.name) +
      "</span>" +
      '<div class="fu-msg-bubble">' +
      escapeHtml(message.message).replace(/\n/g, "<br>") +
      "</div>" +
      '<span class="fu-msg-time">' +
      escapeHtml(message.time) +
      "</span>" +
      "</div>" +
      "</div>"
    );
  }

  function initPopup(config) {
    const toggleButton = document.getElementById("followupToggleBtn");
    const popup = document.getElementById("followupPopup");
    const closeButton = document.getElementById("followupCloseBtn");
    const sessionList = document.getElementById("followupSessionList");
    const threadView = document.getElementById("followupThreadView");
    const backButton = document.getElementById("followupBackBtn");
    const messagesElement = document.getElementById("followupMessages");
    const compose = document.getElementById("followupCompose");
    const threadAvatar = document.getElementById("followupThreadAvatar");
    const threadName = document.getElementById("followupThreadName");
    const threadMeta = document.getElementById("followupThreadMeta");

    if (!toggleButton || !popup || !messagesElement || !compose) {
      return;
    }

    let currentSessionId = null;
    let isLocked = false;
    let msgCount = 0;
    let daysLeft = 0;

    toggleButton.addEventListener("click", function () {
      popup.classList.toggle("active");
      if (popup.classList.contains("active") && typeof lucide !== "undefined") {
        lucide.createIcons();
      }
    });

    closeButton?.addEventListener("click", function () {
      popup.classList.remove("active");
      closeThread();
    });

    document.addEventListener("click", function (event) {
      if (!popup.contains(event.target) && !toggleButton.contains(event.target)) {
        popup.classList.remove("active");
        closeThread();
      }
    });

    sessionList?.addEventListener("click", function (event) {
      const item = event.target.closest(".fu-session-item");
      if (!item) {
        return;
      }

      currentSessionId = parseInt(item.dataset.sessionId || "0", 10);
      threadAvatar.src = item.dataset.avatar || "/assets/img/avatar.png";
      threadName.textContent = item.dataset.name || config.fallbackName || "Contact";
      threadMeta.textContent = item.dataset.date || "";
      messagesElement.innerHTML = '<div class="fu-loading">Loading...</div>';
      threadView?.classList.add("active");

      if (typeof lucide !== "undefined") {
        lucide.createIcons();
      }

      loadMessages();
    });

    backButton?.addEventListener("click", closeThread);

    function closeThread() {
      threadView?.classList.remove("active");
      currentSessionId = null;
    }

    function loadMessages() {
      fetch(config.loadMessagesUrl(currentSessionId))
        .then(function (response) {
          return response.json();
        })
        .then(function (data) {
          if (!data.success) {
            messagesElement.innerHTML = '<div class="fu-empty"><p>Could not load messages.</p></div>';
            return;
          }

          isLocked = data.isLocked;
          msgCount = data.msgCount;
          daysLeft = data.daysLeft;
          renderMessages(data.messages || []);
          updateCompose();
        })
        .catch(function () {
          messagesElement.innerHTML = '<div class="fu-empty"><p>Could not load messages.</p></div>';
        });
    }

    function renderMessages(messages) {
      if (!messages.length) {
        messagesElement.innerHTML =
          '<div class="fu-empty"><i data-lucide="message-circle" stroke-width="1.5"></i><p>' +
          escapeHtml(config.emptyMessage || "No messages yet.") +
          "</p></div>";
        if (typeof lucide !== "undefined") {
          lucide.createIcons();
        }
        return;
      }

      messagesElement.innerHTML = messages.map(renderMessageBubble).join("");
      messagesElement.scrollTop = messagesElement.scrollHeight;
      if (typeof lucide !== "undefined") {
        lucide.createIcons();
      }
    }

    function updateCompose() {
      threadMeta.textContent = createMetaText(msgCount, daysLeft, isLocked);

      if (isLocked) {
        compose.innerHTML =
          '<div class="fu-locked-notice"><p><i data-lucide="lock" style="width:12px;height:12px;vertical-align:middle;"></i> Thread closed</p></div>';
        if (typeof lucide !== "undefined") {
          lucide.createIcons();
        }
        return;
      }

      if (!compose.querySelector("input")) {
        compose.innerHTML =
          '<input type="text" id="followupInput" placeholder="' +
          escapeHtml(config.composePlaceholder || "Type a message...") +
          '" maxlength="1000" />' +
          '<button class="fu-send-btn" id="followupSendBtn" aria-label="Send">' +
          '<i data-lucide="send" stroke-width="2"></i>' +
          "</button>";
      }

      bindCompose();
      if (typeof lucide !== "undefined") {
        lucide.createIcons();
      }
    }

    function bindCompose() {
      const input = compose.querySelector("input");
      const sendButton = compose.querySelector(".fu-send-btn");
      if (!input || !sendButton) {
        return;
      }

      sendButton.onclick = sendMessage;
      input.onkeydown = function (event) {
        if (event.key === "Enter" && !event.shiftKey) {
          event.preventDefault();
          sendMessage();
        }
      };
    }

    function sendMessage() {
      const input = compose.querySelector("input");
      const sendButton = compose.querySelector(".fu-send-btn");
      const text = (input?.value || "").trim();

      if (!input || !sendButton || !currentSessionId || !text || isLocked) {
        return;
      }

      sendButton.disabled = true;
      const formData = new FormData();
      formData.append("session_id", currentSessionId);
      formData.append("message", text);

      fetch(config.sendMessageUrl(currentSessionId), {
        method: "POST",
        body: formData,
      })
        .then(function (response) {
          return response.json();
        })
        .then(function (data) {
          if (!data.success) {
            return;
          }

          input.value = "";
          const empty = messagesElement.querySelector(".fu-empty");
          if (empty) {
            empty.remove();
          }
          messagesElement.insertAdjacentHTML("beforeend", renderMessageBubble(data.message));
          messagesElement.scrollTop = messagesElement.scrollHeight;
          msgCount = data.msgCount;
          daysLeft = data.daysLeft;
          isLocked = msgCount >= 5 || data.isLocked === true;
          updateCompose();
        })
        .finally(function () {
          sendButton.disabled = false;
        });
    }
  }

  function initPage(config) {
    const form = document.getElementById("fuForm");
    const textarea = document.getElementById("fuTextarea");
    const sendButton = document.getElementById("fuSendBtn");
    const messagesElement = document.getElementById("fuMessages");

    if (!messagesElement) {
      return;
    }

    messagesElement.scrollTop = messagesElement.scrollHeight;

    if (!form || !textarea || !sendButton) {
      return;
    }

    form.addEventListener("submit", function (event) {
      event.preventDefault();
      const text = textarea.value.trim();
      if (!text) {
        return;
      }

      sendButton.disabled = true;

      const formData = new FormData();
      formData.append("session_id", config.sessionId);
      formData.append("message", text);

      fetch(config.sendUrl(config.sessionId), {
        method: "POST",
        body: formData,
      })
        .then(function (response) {
          return response.json();
        })
        .then(function (data) {
          if (!data.success) {
            return;
          }

          textarea.value = "";

          const emptyState = messagesElement.querySelector(".followup-empty-state");
          if (emptyState) {
            messagesElement.classList.remove("empty");
            emptyState.remove();
          }

          const bubble =
            '<div class="followup-message message-mine">' +
            '<div class="message-bubble-wrap">' +
            '<span class="message-sender">You</span>' +
            '<div class="message-bubble">' +
            escapeHtml(data.message.text) +
            "</div>" +
            '<span class="message-time">' +
            escapeHtml(data.message.time) +
            "</span>" +
            "</div>" +
            '<img src="' +
            escapeHtml(config.avatarUrl) +
            '" class="message-avatar" alt="" />' +
            "</div>";

          messagesElement.insertAdjacentHTML("beforeend", bubble);
          messagesElement.scrollTop = messagesElement.scrollHeight;

          const hint = form.querySelector(".followup-hint");
          const remaining = 5 - data.msgCount;
          if (hint) {
            hint.textContent =
              remaining +
              " message" +
              (remaining !== 1 ? "s" : "") +
              " remaining · " +
              data.daysLeft +
              " day" +
              (data.daysLeft !== 1 ? "s" : "") +
              " left";
          }

          if (data.msgCount >= 5) {
            window.location.reload();
          }
        })
        .finally(function () {
          sendButton.disabled = false;
        });
    });
  }

  window.NewPathFollowupThread = {
    initPopup: initPopup,
    initPage: initPage,
  };
})();
