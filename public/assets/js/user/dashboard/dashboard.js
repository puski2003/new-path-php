class DashboardApi {
  static async completeTask(taskId) {
    const body = new URLSearchParams();
    body.append("taskId", String(taskId));

    const response = await fetch("/user/recovery/task/complete-ajax", {
      method: "POST",
      headers: {
        "X-Requested-With": "XMLHttpRequest",
      },
      body: body,
    });

    const data = await response.json().catch(function () {
      return null;
    });

    if (!response.ok || !data || data.success !== true) {
      throw new Error((data && data.message) || "Task update failed.");
    }

    return data;
  }
}

class DashboardPage {
  constructor() {
    this.tasksContainer = document.querySelector(".daily-tasks");
    this.pagination = {
      wrapper: document.getElementById("dashTasksPagination"),
      prev: document.getElementById("dashTasksPrev"),
      next: document.getElementById("dashTasksNext"),
      info: document.getElementById("dashTasksPageInfo"),
    };
  }

  init() {
    this.initTaskCompletion();
    this.initTaskPagination();
  }

  initTaskCompletion() {
    if (!this.tasksContainer) {
      return;
    }

    this.tasksContainer.addEventListener("click", async (event) => {
      const taskItem = event.target.closest(".task-item");
      if (!taskItem) {
        return;
      }

      const checkbox = taskItem.querySelector(".task-checkbox");
      const taskText = taskItem.querySelector(".task-text");
      const taskId = Number(taskItem.dataset.taskId || 0);

      if (!taskId || !checkbox || checkbox.classList.contains("completed") || taskItem.dataset.loading === "1") {
        return;
      }

      taskItem.dataset.loading = "1";

      try {
        await DashboardApi.completeTask(taskId);

        checkbox.classList.add("completed");
        checkbox.innerHTML = '<i data-lucide="check" stroke-width="2" color="white"></i>';

        if (taskText) {
          taskText.classList.add("completed");
        }

        if (typeof lucide !== "undefined") {
          lucide.createIcons();
        }
      } catch (error) {
        this.showError(error.message || "Failed to update task.");
      } finally {
        delete taskItem.dataset.loading;
      }
    });
  }

  initTaskPagination() {
    const items = this.tasksContainer
      ? Array.from(this.tasksContainer.querySelectorAll(".task-item"))
      : [];
    const tasksPerPage = 4;

    if (!this.pagination.wrapper || items.length <= tasksPerPage) {
      return;
    }

    let currentPage = 1;
    const totalPages = Math.ceil(items.length / tasksPerPage);

    const render = () => {
      const start = (currentPage - 1) * tasksPerPage;
      const end = start + tasksPerPage;

      items.forEach((item, index) => {
        item.style.display = index >= start && index < end ? "" : "none";
      });

      this.pagination.info.textContent = currentPage + " / " + totalPages;
      this.pagination.prev.disabled = currentPage === 1;
      this.pagination.next.disabled = currentPage === totalPages;
    };

    this.pagination.wrapper.style.display = "flex";

    this.pagination.prev.addEventListener("click", () => {
      if (currentPage > 1) {
        currentPage -= 1;
        render();
      }
    });

    this.pagination.next.addEventListener("click", () => {
      if (currentPage < totalPages) {
        currentPage += 1;
        render();
      }
    });

    render();
  }

  showError(message) {
    if (window.NewPathAlert && typeof window.NewPathAlert.show === "function") {
      window.NewPathAlert.show(message, { type: "error" });
      return;
    }

    window.alert(message);
  }
}

document.addEventListener("DOMContentLoaded", function () {
  new DashboardPage().init();
});
