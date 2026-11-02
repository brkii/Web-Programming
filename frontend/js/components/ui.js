export const esc=v=>String(v??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
export const badge=s=>`<span class="status status-${String(s).toLowerCase().replaceAll(' ','-')}">${esc(s)}</span>`;
export const button=(label,action,extra='',kind='secondary')=>`<button type="button" class="btn btn-${kind}" data-action="${action}" ${extra}>${label}</button>`;
export const empty=(message='No records yet.')=>`<div class="empty"><span>◇</span><h3>Nothing here yet</h3><p>${esc(message)}</p></div>`;
export const metric=(label,value,detail,icon='↗')=>`<article class="metric"><div class="metric-label">${label}<span>${icon}</span></div><strong>${esc(value)}</strong><small>${detail}</small></article>`;
export const table=(headers,rows)=>rows.length?`<div class="table-wrap"><table class="table"><thead><tr>${headers.map(h=>`<th>${h}</th>`).join('')}</tr></thead><tbody>${rows.map(r=>`<tr>${r.map(c=>`<td>${c}</td>`).join('')}</tr>`).join('')}</tbody></table></div>`:empty('Try another filter or add a new record.');
export const field=(name,label,value='',type='text',attrs='')=>`<label class="field">${label}<input class="form-control" name="${name}" type="${type}" value="${esc(value)}" ${attrs}></label>`;
export const select=(name,label,options,value='',attrs='')=>`<label class="field">${label}<select class="form-select" name="${name}" ${attrs}>${options.map(o=>{const [v,l]=Array.isArray(o)?o:[o,o];return `<option value="${esc(v)}" ${String(v)===String(value)?'selected':''}>${esc(l)}</option>`;}).join('')}</select></label>`;
export const textarea=(name,label,value='',attrs='')=>`<label class="field full">${label}<textarea class="form-control" name="${name}" rows="3" ${attrs}>${esc(value)}</textarea></label>`;
export function toast(message){const t=document.querySelector('#toast');t.textContent=message;t.classList.add('visible');clearTimeout(window.toastTimer);window.toastTimer=setTimeout(()=>t.classList.remove('visible'),4000);}
let lastFocus;
export function modal(title,body){lastFocus=document.activeElement;const d=document.querySelector('#modal');d.innerHTML=`<header class="modal-heading"><h2 id="modal-title">${esc(title)}</h2>${button('×','close','aria-label="Close dialog"','icon')}</header>${body}`;d.showModal();d.addEventListener('close',()=>lastFocus?.focus(),{once:true});}
export const closeModal=()=>document.querySelector('#modal').close();
// Compact body-style drawings keep vehicle cards useful without a promotional photo.
export const car=(vehicle={})=>{
 const model=String(vehicle?.model||'').toLowerCase();
 const suv=/xc60|xc90|suv|q[3578]|x[1357]/.test(model);
 const hatch=/golf|a3|hatch/.test(model);
 const roof=suv?'M35 52 53 26Q57 22 66 22H132Q143 22 151 32L170 49':hatch?'M33 54 47 30Q50 26 59 26H122Q132 26 141 35L160 49':'M30 53 62 31Q68 27 77 27H119Q129 27 140 36L159 48';
 const glass=suv?'M55 46 65 29H94V46ZM101 29H130Q137 29 142 36L151 46H101Z':hatch?'M53 47 60 33H90V47ZM97 33H120L139 47H97Z':'M59 47 77 34H99V47ZM106 34H120L141 47H106Z';
 return `<div class="vehicle-drawing" aria-hidden="true"><svg viewBox="0 0 220 100" fill="none"><path d="M17 79H205" stroke="currentColor" opacity=".12"/><path d="${roof}" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/><path d="M35 52 19 56 15 65 17 73H34a15 15 0 0 1 30 0h91a15 15 0 0 1 30 0h15l3-12-10-8-33-5" fill="currentColor" fill-opacity=".07" stroke="currentColor" stroke-width="2.5" stroke-linejoin="round"/><path d="${glass}" fill="currentColor" fill-opacity=".18"/><path d="M96 51v18M151 53l-5 16M105 54h10" stroke="currentColor" stroke-opacity=".4" stroke-width="1.5"/><path d="m19 57 12-2v6H17" fill="#ed293e"/><path d="m190 55 10 4-2 4-11-3" fill="currentColor"/><path d="M72 76h75" stroke="#ed293e" stroke-width="2"/>${[49,170].map(x=>`<circle cx="${x}" cy="73" r="11" fill="var(--panel)" stroke="currentColor" stroke-width="2.5"/><circle cx="${x}" cy="73" r="5" stroke="currentColor" stroke-opacity=".45" stroke-width="2"/>`).join('')}</svg></div>`;
};
