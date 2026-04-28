"use strict";
const Toast = {
  show(message, type = "success", duration = 3500) {
    const icons = { success: "✅", danger: "❌", warning: "⚠️", info: "ℹ️" };
    const id = "toast-" + Date.now();
    const html = `
      <div id="${id}" class="toast toast-${type} align-items-center border-0 mb-2" role="alert" aria-live="assertive">
        <div class="d-flex">
          <div class="toast-body fw-semibold">
            <span>${icons[type] || ""}</span> ${message}
          </div>
          <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
      </div>`;
    let container = document.querySelector(".toast-container");
    if (!container) {
      container = document.createElement("div");
      container.className = "toast-container position-fixed top-0 end-0 p-3";
      document.body.appendChild(container);
    }
    container.insertAdjacentHTML("beforeend", html);
    const toastEl = document.getElementById(id);
    const bsToast = new bootstrap.Toast(toastEl, { delay: duration });
    bsToast.show();
    toastEl.addEventListener("hidden.bs.toast", () => toastEl.remove());
  },
};

const FormValidator = {
  rules: {
    title: { required: true, minLength: 3, maxLength: 100 },
    description: { required: false, maxLength: 500 },
    priority: { required: true },
    status: { required: true },
    category: { required: true },
    due_date: { required: false },
  },

  validate(form) {
    let valid = true;
    form.querySelectorAll(".is-invalid,.is-valid").forEach((el) => {
      el.classList.remove("is-invalid", "is-valid");
    });
    form
      .querySelectorAll(".invalid-feedback")
      .forEach((el) => (el.textContent = ""));

    const fields = form.querySelectorAll("[data-validate]");
    fields.forEach((field) => {
      const name = field.name;
      const value = field.value.trim();
      const rule = this.rules[name];
      if (!rule) return;
      const feedback =
        field.parentElement.querySelector(".invalid-feedback") ||
        form.querySelector(`[data-feedback="${name}"]`);

      let error = "";
      if (rule.required && !value) {
        error = "Este campo es obligatorio.";
      } else if (value && rule.minLength && value.length < rule.minLength) {
        error = `Mínimo ${rule.minLength} caracteres.`;
      } else if (value && rule.maxLength && value.length > rule.maxLength) {
        error = `Máximo ${rule.maxLength} caracteres.`;
      }

      if (error) {
        field.classList.add("is-invalid");
        if (feedback) feedback.textContent = error;
        valid = false;
      } else if (value) {
        field.classList.add("is-valid");
      }
    });

    const titleField = form.querySelector('[name="title"]');
    if (titleField) {
      const counter = form.querySelector("#titleCounter");
      if (counter) counter.textContent = `${titleField.value.length}/100`;
    }

    return valid;
  },

  attachLiveValidation(form) {
    form.querySelectorAll("[data-validate]").forEach((field) => {
      field.addEventListener("input", () => {
        const name = field.name;
        const value = field.value.trim();
        const rule = this.rules[name];
        if (!rule) return;
        field.classList.remove("is-invalid", "is-valid");
        const feedback = field.parentElement.querySelector(".invalid-feedback");

        let error = "";
        if (rule.required && !value) {
          error = "Este campo es obligatorio.";
        } else if (value && rule.minLength && value.length < rule.minLength) {
          error = `Mínimo ${rule.minLength} caracteres.`;
        } else if (value && rule.maxLength && value.length > rule.maxLength) {
          error = `Máximo ${rule.maxLength} caracteres.`;
        }

        if (error) {
          field.classList.add("is-invalid");
          if (feedback) feedback.textContent = error;
        } else if (value) {
          field.classList.add("is-valid");
        }
      });
    });

    const titleField = form.querySelector('[name="title"]');
    if (titleField) {
      titleField.addEventListener("input", () => {
        const counter = form.querySelector("#titleCounter");
        if (counter) counter.textContent = `${titleField.value.length}/100`;
      });
    }
  },
};

function confirmDelete(taskId, taskTitle) {
  const modal = document.getElementById("confirmModal");
  if (modal) {
    document.getElementById("confirmTaskTitle").textContent = taskTitle;
    document.getElementById("confirmDeleteBtn").onclick = () => {
      deleteTask(taskId);
      bootstrap.Modal.getInstance(modal).hide();
    };
    new bootstrap.Modal(modal).show();
  } else {
    if (
      confirm(
        `¿Eliminar la tarea "${taskTitle}"?\nEsta acción no se puede deshacer.`,
      )
    ) {
      deleteTask(taskId);
    }
  }
}

const API = {
  base: "api/",

  async request(endpoint, method = "GET", data = null) {
    const opts = {
      method,
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
    };
    if (data) opts.body = JSON.stringify(data);
    const res = await fetch(this.base + endpoint, opts);
    const json = await res.json();
    return json;
  },

  getTasks: (params = "") =>
    API.request("tasks.php" + (params ? "?" + params : "")),
  getTask: (id) => API.request(`tasks.php?id=${id}`),
  createTask: (d) => API.request("tasks.php", "POST", d),
  updateTask: (id, d) => API.request(`tasks.php?id=${id}`, "PUT", d),
  deleteTask: (id) => API.request(`tasks.php?id=${id}`, "DELETE"),
  getStats: () => API.request("stats.php"),
  getCategories: () => API.request("categories.php"),
};

function pagesPrefix() {
  return window.location.pathname.includes("/pages/") ? "" : "pages/";
}

let allTasks = [];
let currentFilters = { search: "", priority: "", status: "", category: "" };

async function loadTasks() {
  const tableBody = document.getElementById("tasksTableBody");
  if (!tableBody) return;

  try {
    showTableLoading(tableBody);
    const params = buildParams();
    const res = await API.getTasks(params);

    allTasks = res.data || [];
    renderTable(allTasks);
    updateCount(allTasks.length);
  } catch (e) {
    console.error(e);
    showTableError(tableBody);
  }
}

function buildParams() {
  const p = [];
  if (currentFilters.search)
    p.push("search=" + encodeURIComponent(currentFilters.search));
  if (currentFilters.priority) p.push("priority=" + currentFilters.priority);
  if (currentFilters.status)
    p.push("status=" + encodeURIComponent(currentFilters.status));
  if (currentFilters.category)
    p.push("category=" + encodeURIComponent(currentFilters.category));
  return p.join("&");
}

function renderTable(tasks) {
  const tbody = document.getElementById("tasksTableBody");
  if (!tbody) return;

  if (!tasks.length) {
    tbody.innerHTML = `<tr><td colspan="8" class="text-center py-5">
      <div class="empty-state">
        <div class="icon">📋</div>
        <h5>No hay tareas que mostrar</h5>
        <p class="table-hint">Cambia los filtros o crea una nueva tarea.</p>
        <a href="${pagesPrefix()}form.php" class="btn btn-primary btn-sm mt-2">
          <i class="bi bi-plus-lg"></i> Nueva Tarea
        </a>
      </div>
    </td></tr>`;
    return;
  }

  const today = new Date().toISOString().split("T")[0];
  tbody.innerHTML = tasks
    .map((t, i) => {
      const overdueClass =
        t.due_date && t.due_date < today && t.status !== "completada"
          ? "overdue"
          : t.due_date === today
            ? "due-today"
            : "";
      return `
    <tr class="task-row animate-in" style="animation-delay:${i * 0.04}s"
        onclick="window.location='${pagesPrefix()}detail.php?id=${t.id}'" title="Ver detalle">
      <td><span class="row-num">${t.row_num ?? i + 1}</span></td>
      <td>
        <div class="task-title-cell">${escHtml(t.title)}</div>
        ${t.description ? `<div class="task-desc-cell">${escHtml(t.description)}</div>` : ""}
      </td>
      <td><span class="badge-pill priority-${t.priority}">${capitalize(t.priority)}</span></td>
      <td><span class="badge-pill status-${t.status.replace(" ", "-")}">${capitalize(t.status)}</span></td>
      <td><span class="category-badge">${escHtml(t.category)}</span></td>
      <td class="${overdueClass}" style="font-size:.85rem">${formatDate(t.due_date)}</td>
      <td style="color:var(--text2)" style="font-size:.8rem">${formatDateTime(t.created_at)}</td>
      <td onclick="event.stopPropagation()">
        <div class="d-flex gap-1">
          <a href="${pagesPrefix()}form.php?id=${t.id}" class="btn btn-icon btn-sm btn-outline-primary" title="Editar tarea">
            <i class="bi bi-pencil"></i>
          </a>
          <button onclick="confirmDelete(${t.id}, '${escHtml(t.title).replace(/'/g, "\\\'")}')"
            class="btn btn-icon btn-sm btn-outline-danger" title="Eliminar tarea">
            <i class="bi bi-trash"></i>
          </button>
          <a href="${pagesPrefix()}detail.php?id=${t.id}" class="btn btn-icon btn-sm btn-outline-secondary" title="Ver detalle">
            <i class="bi bi-eye"></i>
          </a>
        </div>
      </td>
    </tr>`;
    })
    .join("");
}

function showTableLoading(tbody) {
  tbody.innerHTML = `<tr><td colspan="8" class="text-center py-4">
    <div class="spinner-border spinner-border-sm text-primary me-2"></div>
    <span style="color:var(--text2)">Cargando tareas...</span>
  </td></tr>`;
}
function showTableError(tbody) {
  tbody.innerHTML = `<tr><td colspan="8" class="text-center py-4 text-danger">
    <i class="bi bi-exclamation-triangle me-2"></i>Error al cargar las tareas.
  </td></tr>`;
}
function updateCount(n) {
  const el = document.getElementById("taskCount");
  if (el) el.textContent = `${n} tarea${n !== 1 ? "s" : ""}`;
}

let searchTimeout;
function onSearchInput(val) {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    currentFilters.search = val;
    loadTasks();
  }, 280);
}
function onFilterChange(key, val) {
  currentFilters[key] = val;
  loadTasks();
}
function clearFilters() {
  currentFilters = { search: "", priority: "", status: "", category: "" };
  document.getElementById("searchInput") &&
    (document.getElementById("searchInput").value = "");
  document.getElementById("filterPriority") &&
    (document.getElementById("filterPriority").value = "");
  document.getElementById("filterStatus") &&
    (document.getElementById("filterStatus").value = "");
  document.getElementById("filterCategory") &&
    (document.getElementById("filterCategory").value = "");
  loadTasks();
  Toast.show("Filtros limpiados", "info", 2000);
}

async function deleteTask(id) {
  try {
    const res = await API.deleteTask(id);
    if (res.success) {
      Toast.show("Tarea eliminada correctamente", "success");
      // DOM remove row
      const row = document.querySelector(`tr[data-id="${id}"]`);
      if (row) {
        row.style.transition = "opacity 0.3s, transform 0.3s";
        row.style.opacity = "0";
        row.style.transform = "translateX(20px)";
        setTimeout(() => row.remove(), 300);
      }
      loadTasks();
      if (typeof loadStats === "function") loadStats();
    } else {
      Toast.show(res.message || "Error al eliminar", "danger");
    }
  } catch (e) {
    Toast.show("Error de conexión", "danger");
  }
}

async function quickStatusChange(id, newStatus, btn) {
  try {
    const res = await API.updateTask(id, { status: newStatus });
    if (res.success) {
      Toast.show(
        `Estado actualizado: ${capitalize(newStatus)}`,
        "success",
        2000,
      );
      loadTasks();
      if (typeof loadStats === "function") loadStats();
    }
  } catch (e) {
    Toast.show("Error al actualizar", "danger");
  }
}

async function submitTaskForm(form, isEdit, taskId) {
  if (!FormValidator.validate(form)) {
    Toast.show("Corrige los errores del formulario", "warning");
    return;
  }

  const data = {
    title: form.querySelector('[name="title"]').value.trim(),
    description: form.querySelector('[name="description"]').value.trim(),
    priority: form.querySelector('[name="priority"]').value,
    status: form.querySelector('[name="status"]').value,
    category: form.querySelector('[name="category"]').value,
    due_date: form.querySelector('[name="due_date"]').value,
  };

  const btn = form.querySelector('[type="submit"]');
  btn.disabled = true;
  btn.innerHTML =
    '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';

  try {
    const res = isEdit
      ? await API.updateTask(taskId, data)
      : await API.createTask(data);

    if (res.success) {
      Toast.show(isEdit ? "Tarea actualizada ✓" : "Tarea creada ✓", "success");
      const redirectTo = window.location.pathname.includes("/pages/")
        ? "../index.php"
        : "index.php";
      setTimeout(() => (window.location.href = redirectTo), 900);
    } else {
      Toast.show(res.message || "Error al guardar", "danger");
      btn.disabled = false;
      btn.innerHTML = isEdit
        ? '<i class="bi bi-check-lg"></i> Guardar cambios'
        : '<i class="bi bi-plus-lg"></i> Crear tarea';
    }
  } catch (e) {
    Toast.show("Error de conexión", "danger");
    btn.disabled = false;
    btn.innerHTML = "Guardar";
  }
}

async function loadStats() {
  try {
    const res = await API.getStats();
    if (!res.success) return;
    const s = res.data;

    setElText("statTotal", s.total);
    setElText("statPendiente", s.pendiente);
    setElText("statProgreso", s.en_progreso);
    setElText("statCompletada", s.completada);

    const pct = s.total > 0 ? Math.round((s.completada / s.total) * 100) : 0;
    const bar = document.getElementById("completionBar");
    if (bar) {
      bar.style.width = pct + "%";
      bar.textContent = pct + "%";
    }

    if (window.taskChart && s.by_priority) {
      updateChart(s.by_priority);
    }

    if (s.recent) renderRecentTasks(s.recent);
  } catch (e) {
    console.error("Stats error", e);
  }
}

function renderRecentTasks(tasks) {
  const el = document.getElementById("recentTasksList");
  if (!el || !tasks) return;
  el.innerHTML = tasks
    .map(
      (t) => `
    <tr>
      <td><a href="${pagesPrefix()}detail.php?id=${t.id}" class="text-decoration-none text-light fw-semibold">${escHtml(t.title)}</a></td>
      <td><span class="badge-pill priority-${t.priority}">${capitalize(t.priority)}</span></td>
      <td><span class="badge-pill status-${t.status.replace(" ", "-")}">${capitalize(t.status)}</span></td>
      <td class="table-hint">${formatDate(t.due_date)}</td>
    </tr>`,
    )
    .join("");
}

function initChart(byPriority) {
  const ctx = document.getElementById("priorityChart");
  if (!ctx) return;
  window.taskChart = new Chart(ctx, {
    type: "doughnut",
    data: {
      labels: ["Alta", "Media", "Baja"],
      datasets: [
        {
          data: [
            byPriority.alta || 0,
            byPriority.media || 0,
            byPriority.baja || 0,
          ],
          backgroundColor: ["#ef4444", "#f59e0b", "#10b981"],
          borderColor: "transparent",
          borderWidth: 0,
          hoverOffset: 8,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: "68%",
      plugins: {
        legend: {
          position: "bottom",
          labels: { color: "#9ca3af", padding: 16, font: { size: 12 } },
        },
        tooltip: {
          callbacks: {
            label: (ctx) =>
              ` ${ctx.label}: ${ctx.raw} tarea${ctx.raw !== 1 ? "s" : ""}`,
          },
        },
      },
    },
  });
}

function updateChart(byPriority) {
  if (!window.taskChart) return;
  window.taskChart.data.datasets[0].data = [
    byPriority.alta || 0,
    byPriority.media || 0,
    byPriority.baja || 0,
  ];
  window.taskChart.update("active");
}

function escHtml(str) {
  const d = document.createElement("div");
  d.textContent = str || "";
  return d.innerHTML;
}

function capitalize(str) {
  if (!str) return "";
  return str.charAt(0).toUpperCase() + str.slice(1);
}

function formatDate(dateStr) {
  if (!dateStr) return "—";
  const [y, m, d] = dateStr.split("-");
  if (!y) return "—";
  return `${d}/${m}/${y}`;
}

function formatDateTime(dtStr) {
  if (!dtStr) return "—";
  return dtStr.replace("T", " ").substring(0, 16);
}

function setElText(id, val) {
  const el = document.getElementById(id);
  if (el) {
    const target = parseInt(val) || 0;
    const current = parseInt(el.textContent) || 0;
    if (target === current) return;
    let start = current,
      step = (target - current) / 20;
    let count = 0;
    const interval = setInterval(() => {
      start += step;
      count++;
      el.textContent = Math.round(start);
      if (count >= 20) {
        el.textContent = target;
        clearInterval(interval);
      }
    }, 25);
  }
}

// ── Búsqueda rápida global del navbar ──────────────────────────────────────
let _qsTimer;
async function quickSearchFn(val) {
  const drop = document.getElementById("quickSearchResults");
  if (!drop) return;
  if (!val || val.trim().length < 2) {
    drop.style.display = "none";
    drop.innerHTML = "";
    return;
  }
  clearTimeout(_qsTimer);
  _qsTimer = setTimeout(async () => {
    drop.style.display = "block";
    drop.innerHTML =
      '<div style="padding:1rem;text-align:center"><div class="spinner-border spinner-border-sm text-primary"></div></div>';
    try {
      const res = await fetch(
        API.base +
          "tasks.php?search=" +
          encodeURIComponent(val.trim()) +
          "&limit=8",
      );
      const data = await res.json();
      const tasks = data.data || [];
      if (!tasks.length) {
        drop.innerHTML = `<div style="padding:1.2rem;text-align:center;color:var(--text3);font-size:.85rem">Sin resultados para "<strong>${escHtml(val)}</strong>"</div>`;
        return;
      }
      const prefix = window.location.pathname.includes("/pages/")
        ? ""
        : "pages/";
      drop.innerHTML =
        `<div style="padding:.4rem .9rem;font-size:.67rem;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:var(--text3);border-bottom:1px solid var(--border)">${tasks.length} resultado${tasks.length !== 1 ? "s" : ""}</div>` +
        tasks
          .map(
            (t) =>
              `<a href="${prefix}detail.php?id=${t.id}" style="display:flex;align-items:center;gap:.7rem;padding:.6rem 1rem;border-bottom:1px solid var(--border);text-decoration:none;transition:var(--t)" onmouseover="this.style.background='rgba(99,106,248,.08)'" onmouseout="this.style.background='transparent'">
                <div style="width:28px;height:28px;border-radius:50%;background:var(--accent-bg);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                  <i class="bi bi-file-text" style="font-size:.75rem;color:var(--accent2)"></i>
                </div>
                <div style="flex:1;min-width:0">
                  <div style="font-size:.875rem;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${escHtml(t.title)}</div>
                  <div style="font-size:.73rem;color:var(--text3)">${escHtml(t.category)} · <span class="badge-pill priority-${t.priority}" style="font-size:.6rem">${capitalize(t.priority)}</span></div>
                </div>
                <span class="badge-pill status-${t.status.replace(" ", "-")}" style="font-size:.62rem">${capitalize(t.status)}</span>
              </a>`,
          )
          .join("");
    } catch (e) {
      drop.innerHTML =
        '<div style="padding:1rem;color:var(--text3);font-size:.85rem">Error al buscar</div>';
    }
  }, 220);
}

document.addEventListener("click", (e) => {
  if (
    !e.target.closest("#quickSearch") &&
    !e.target.closest("#quickSearchResults")
  ) {
    const drop = document.getElementById("quickSearchResults");
    if (drop) drop.style.display = "none";
  }
});
document.addEventListener("keydown", (e) => {
  if ((e.ctrlKey || e.metaKey) && e.key === "n") {
    e.preventDefault();
    window.location.href = pagesPrefix() + "form.php";
  }
  if (e.key === "Escape") {
    const modal = document.querySelector(".modal.show");
    if (modal) bootstrap.Modal.getInstance(modal)?.hide();
  }
});

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".animate-in").forEach((el, i) => {
    el.style.animationDelay = i * 0.07 + "s";
  });

  if (document.getElementById("tasksTableBody")) {
    loadTasks();

    setInterval(loadTasks, 45000);
  }

  if (document.getElementById("statTotal")) {
    loadStats();
  }

  const taskForm = document.getElementById("taskForm");
  if (taskForm) {
    FormValidator.attachLiveValidation(taskForm);
  }

  document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => {
    new bootstrap.Tooltip(el);
  });

  if (document.getElementById("dashboardWelcome")) {
    setTimeout(
      () => Toast.show("TaskMaster cargado correctamente ✓", "success", 2500),
      600,
    );
  }
});
