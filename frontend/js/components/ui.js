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
export const car=()=>`<div class="vehicle-placeholder" aria-label="Vehicle record"><span>VEHICLE RECORD</span><svg viewBox="0 0 240 64" aria-hidden="true"><path d="M12 54h216M35 40h170M55 26h130M85 12h70" fill="none" stroke="currentColor" stroke-width="2"/></svg></div>`;
