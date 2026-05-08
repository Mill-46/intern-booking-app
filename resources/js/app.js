import Chart from "chart.js/auto";

window.Chart = Chart;

const loadingOverlay = document.getElementById("loadingOverlay");
const confirmModal = document.getElementById("confirmModal");
const confirmMessage = document.getElementById("confirmMessage");
const confirmOk = document.getElementById("confirmOk");
const confirmCancel = document.getElementById("confirmCancel");

const state = {
    pendingAction: null,
};

function setLoading(isLoading) {
    if (!loadingOverlay) return;

    loadingOverlay.classList.toggle("active", isLoading);
}

function openConfirm(message) {
    if (!confirmModal || !confirmMessage) return;

    confirmMessage.textContent = message;
    state.pendingAction = null;
    confirmModal.classList.add("active");
}

function closeConfirm() {
    if (!confirmModal) return;

    confirmModal.classList.remove("active");
    state.pendingAction = null;
}

function confirmWith(message, action) {
    openConfirm(message);
    state.pendingAction = action;

    if (confirmOk) confirmOk.disabled = false;
}

function readConfirmTrigger(eventTarget) {
    if (!eventTarget) return null;

    const trigger = eventTarget.closest?.("[data-confirm]");
    if (!trigger) return null;

    return trigger;
}

if (confirmOk) {
    confirmOk.addEventListener("click", () => {
        if (typeof state.pendingAction === "function") {
            state.pendingAction();
        }
        closeConfirm();
    });
}

if (confirmCancel) {
    confirmCancel.addEventListener("click", closeConfirm);
}

document.addEventListener("click", (event) => {
    const trigger = readConfirmTrigger(event.target);
    if (!trigger) return;

    event.preventDefault();

    const message =
        trigger.getAttribute("data-confirm") ||
        "Apakah Anda yakin ingin melanjutkan?";
    const tag = trigger.tagName.toLowerCase();

    // Button triggers (typically inside a form)
    if (tag === "button") {
        const form = trigger.form;

        confirmWith(message, () => {
            if (!form) return;
            setLoading(true);
            form.submit();
        });

        return;
    }

    // Link triggers
    if (tag === "a") {
        const href = trigger.getAttribute("href");

        confirmWith(message, () => {
            if (!href) return;
            setLoading(true);
            window.location.assign(href);
        });
    }
});

document.addEventListener("submit", (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement)) return;

    setLoading(true);

    const submitButton = form.querySelector('button[type="submit"]');
    if (!submitButton) return;

    submitButton.setAttribute("disabled", "disabled");
    submitButton.dataset.originalText = submitButton.textContent || "";
    submitButton.textContent = "Memproses...";
});

window.addEventListener("pageshow", () => setLoading(false));
window.addEventListener("load", () => {
    setLoading(false);
    document.body.classList.remove("is-loading");
});
