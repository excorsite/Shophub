// assets/js/app.js
function q(sel, el=document){ return el.querySelector(sel); }
function qa(sel, el=document){ return [...el.querySelectorAll(sel)]; }

// currency helper
window.formatINR = (n) => '₹ ' + Number(n).toFixed(2);

// simple search hook (optional: add an #searchInput anywhere)
const searchEl = q('#searchInput');
if (searchEl){
  searchEl.addEventListener('input', () => {
    const qv = searchEl.value.toLowerCase().trim();
    qa('[data-card]').forEach(card => {
      const hay = card.textContent.toLowerCase();
      card.style.display = hay.includes(qv) ? '' : 'none';
    });
  });
}
