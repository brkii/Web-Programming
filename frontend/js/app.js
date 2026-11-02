import {db,session,find,save,remove,updateRequest,visibleRequests,visibleTickets,money} from './services/store.js';
import {esc,badge,button,field,select,textarea,modal,closeModal,toast,table} from './components/ui.js';
import {routes,currentRoute,navigate,loadView} from './router.js';
import {renderPage,filterResults,vehicleName,serviceName} from './pages.js';
import {requestStatuses} from './mockData.js';
const customerNav=[['customer-dashboard','Overview','◈'],['my-vehicles','My vehicles','◇'],['request-service','Request service','＋'],['my-requests','My requests','≡'],['service-history','Service history','◷'],['customer-support','Support center','◌'],['profile','Profile','◎']];
const adminNav=[['admin-dashboard','Overview','◈'],['manage-customers','Customers','◎'],['manage-vehicles','Vehicles','◇'],['manage-requests','Service requests','≡'],['manage-work-orders','Work orders','▤'],['manage-services','Services','⊞'],['manage-parts','Parts inventory','▦'],['manage-support','Support tickets','◌']];
const mechanicNav=[['mechanic-dashboard','My workbench','◈'],['manage-work-orders','Work orders','▤'],['manage-support','Support tickets','◌'],['profile','Profile','◎']];
const brand='<a class="brand" href="#home" aria-label="PitStop OS home"><svg class="brand-symbol" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 64" fill="none">  <path fill="currentColor" d="M9 33 29 27C43 11 61 9 76 18L92 28 113 33 120 40 109 37 88 32 73 22C60 14 46 17 32 31L9 36Z"/>  <path d="m10 40-2 7h10a12 12 0 0 1 24 0h43a12 12 0 0 1 24 0h10l2-6" stroke="currentColor" stroke-width="3.5" stroke-linejoin="round"/>  <path d="M45 32h32" stroke="currentColor" stroke-width="2.5"/>  <path d="m8 34 15-2-3 6H8Z" fill="#ed293e"/>  <path d="m109 36 10 3-2 3-9-3Z" fill="currentColor"/>  <circle cx="30" cy="47" r="7" stroke="currentColor" stroke-width="3.5"/>  <circle cx="97" cy="47" r="7" stroke="currentColor" stroke-width="3.5"/>  <path d="M47 52h32" stroke="#ed293e" stroke-width="3"/></svg><span class="brand-wordmark"><span class="brand-name">pitstop<span class="brand-period">.</span></span><span class="brand-os">OS</span></span></a>';
let rendering=0;
function setTheme(theme){
 document.documentElement.dataset.theme=theme;
 document.documentElement.style.colorScheme=theme;
 try{localStorage.setItem('pitstop-theme',theme);}catch{}
 document.querySelectorAll('[data-action="theme"]').forEach(b=>{b.textContent=theme==='light'?'Dark mode':'Light mode';b.setAttribute('aria-label','Switch to '+(theme==='light'?'dark':'light')+' mode');});
}
let initialTheme='dark';
try{const saved=localStorage.getItem('pitstop-theme');if(['light','dark'].includes(saved))initialTheme=saved;}catch{}
setTheme(initialTheme);
const themeControl=()=>`<button class="theme-toggle" type="button" data-action="theme" aria-label="Switch to ${document.documentElement.dataset.theme==='light'?'dark':'light'} mode">${document.documentElement.dataset.theme==='light'?'Dark mode':'Light mode'}</button>`;
async function render(){
 const n=++rendering;const {name,params}=currentRoute();const app=document.querySelector('#app');
 if(!routes[name]){app.innerHTML=`<main id="main" class="not-found"><h1>That road ends here.</h1><a href="#home">Back to PitStop OS</a></main>`;return;}
 try{
  const view=await loadView(name);if(n!==rendering||view===null)return;
  const isPublic=['home','login','register'].includes(name);
  if(isPublic)app.innerHTML=`<header class="public-nav">${brand}<nav aria-label="Public navigation">${themeControl()}<a href="#login">Log in</a><a class="btn btn-primary" href="#register">Get started ↗</a></nav></header><main id="main">${view}</main><footer>${brand}<span>Your vehicle. Our priority.</span><span>Static prototype</span></footer>`;
  else {
   const nav=session.role==='admin'?adminNav:session.role==='mechanic'?mechanicNav:customerNav;
   const allowed=nav.some(([r])=>r===name)||['vehicle-details','support-ticket-details','profile'].includes(name);
   if(!allowed){navigate(session.role==='admin'?'admin-dashboard':session.role==='mechanic'?'mechanic-dashboard':'customer-dashboard');return;}
   app.innerHTML=`<aside class="sidebar">${brand}<div class="workspace-label">${session.role==='customer'?'PERSONAL GARAGE':'WORKSHOP MANAGEMENT'}</div><nav aria-label="Main navigation">${nav.map(([r,l,i])=>`<a href="#${r}" class="nav-item ${r===name?'active':''}" ${r===name?'aria-current="page"':''}><span>${i}</span>${l}${r===name?'<b>•</b>':''}</a>`).join('')}</nav><div class="sidebar-bottom"><a class="profile-link" href="#profile"><span class="avatar">${esc(find('users',session.userId).full_name.split(' ').map(s=>s[0]).join(''))}</span><span>${esc(find('users',session.userId).full_name)}<small>${session.role}</small></span></a><a href="#home" class="exit-link">← Back to home</a></div></aside><div class="workspace"><header class="topbar"><button class="btn btn-icon mobile-menu" data-action="menu" aria-label="Toggle navigation" aria-expanded="false">☰</button><span>Workspace <span class="breadcrumb">/ ${esc(routes[name])}</span></span>${themeControl()}<div class="demo-switch"><label for="role">VIEW AS</label><select id="role" aria-label="Preview role">${['customer','admin','mechanic'].map(r=>`<option ${r===session.role?'selected':''}>${r}</option>`).join('')}</select></div></header><main id="main">${view}</main><div class="workspace-footer">PITSTOP OS <span>Static prototype · Local changes only</span></div></div>`;
   if(!name.endsWith('dashboard'))document.querySelector('#page-title').textContent=routes[name];
   renderPage(name,params);
  }
  document.title=`${routes[name]} · PitStop OS`;window.scrollTo(0,0);
 }catch(error){app.innerHTML=`<main class="not-found"><h1>We couldn’t load this screen.</h1><p>${esc(error.message)}</p><p>Run the frontend through a local HTTP server.</p><a href="#home">Try home</a></main>`;}
}
function editor(tableName,id){
 const row=id?find(tableName,id):{};if(!row)return;
 let content='';const required='required maxlength="100"';
 if(tableName==='vehicles')content=`<div class="full vin-preview"><p>VIN preview · simulated NHTSA response</p>${field('vin','VIN (optional)',row.vin,'text','maxlength="17" pattern="[A-HJ-NPR-Z0-9]{17}"')}<div class="card-actions">${button('Decode VIN','decode')}${button('Use sample VIN','demo-vin')}</div><small>Only sample VIN 1HGCM82633A004352 is recognized. Manual entry is always available.</small></div>${session.role==='admin'?select('user_id','Owner',db.users.filter(u=>u.role==='customer').map(u=>[u.id,u.full_name]),row.user_id,'required'):''}${field('make','Manufacturer',row.make,'text',required)}${field('model','Model',row.model,'text',required)}${field('year','Production year',row.year||2022,'number','required min="1886" max="2027" step="1"')}${field('license_plate','License plate',row.license_plate,'text',required)}${field('mileage','Mileage (km)',row.mileage??0,'number','required min="0" max="9999999" step="1"')}${select('fuel_type','Fuel type',['Petrol','Diesel','Hybrid','Electric'],row.fuel_type)}${select('transmission','Transmission',['Automatic','Manual'],row.transmission)}`;
 if(tableName==='users')content=field('full_name','Full name',row.full_name,'text',required)+field('email','Email',row.email,'email','required')+field('city','City',row.city,'text',required);
 if(tableName==='parts')content=field('name','Part name',row.name,'text',required)+field('sku','SKU',row.sku,'text',required)+field('category','Category',row.category,'text',required)+field('unit_price','Unit price (BAM)',row.unit_price??0,'number','required min="0" step="0.01"')+field('quantity','Quantity',row.quantity??0,'number','required min="0" step="1"')+field('minimum_stock','Minimum stock',row.minimum_stock??5,'number','required min="0" step="1"');
 if(tableName==='services')content=field('name','Service name',row.name,'text',required)+textarea('description','Description',row.description,'required maxlength="500"')+field('standard_price','Standard price (BAM)',row.standard_price??0,'number','required min="0" step="0.01"')+field('estimated_duration','Duration (minutes)',row.estimated_duration??30,'number','required min="1" max="10080" step="1"');
 modal(`${id?'Edit':'Add'} ${tableName==='users'?'customer':tableName.slice(0,-1)}`,`<form id="entity-form" data-table="${tableName}" data-id="${id||''}" class="form-grid">${content}<div class="full form-actions">${button('Cancel','close')}<button class="btn btn-primary">Save changes</button></div><p class="form-error full" role="alert"></p></form>`);
}
function requestDetails(id){
 const r=visibleRequests().find(r=>r.id===Number(id));if(!r)return;
 const order=db.orders.find(o=>o.service_request_id===r.id);
 const steps=requestStatuses.filter(s=>s!=='Cancelled');
 modal(`Service request #SR-${r.id}`,`<h3>${esc(vehicleName(r.vehicle_id))}</h3><p>${esc(serviceName(r.service_id))} · ${esc(r.preferred_date)}</p><p>${esc(r.description)}</p><p>${esc(r.notes)}</p>${badge(r.status)}<ol class="timeline">${steps.map((s,i)=>`<li class="${r.status!=='Cancelled'&&i<=steps.indexOf(r.status)?'done':''}">${s}</li>`).join('')}</ol>${session.role==='admin'?`<form id="request-status" data-id="${r.id}">${select('status','Request status',requestStatuses,r.status)}${select('mechanic_id','Assigned mechanic',[['','Unassigned'],...db.users.filter(u=>u.role==='mechanic').map(u=>[u.id,u.full_name])],order?.mechanic_id||'')}<button class="btn btn-primary">Update request</button><p class="muted">Approve to create a work order, or choose Cancelled to reject.</p></form>`:''}`);
}
function orderDetails(id){
 const o=find('orders',id);if(!o)return;const r=find('requests',o.service_request_id);
 if(session.role==='customer'&&r.requested_by!==session.userId)return;
 const items=db.items.filter(i=>i.service_order_id===o.id);
 modal(`Work order #WO-${o.id}`,`<h3>${esc(vehicleName(r.vehicle_id))}</h3><p>${esc(find('users',r.requested_by).full_name)} · Mechanic: ${esc(find('users',o.mechanic_id)?.full_name||'Unassigned')}</p><p>${esc(o.diagnosis)}</p>${table(['Labor / part','Qty','Unit price','Total'],items.map(i=>[esc(i.service_id?serviceName(i.service_id):find('parts',i.part_id)?.name),i.quantity,money(i.unit_price),money(i.quantity*i.unit_price)]))}<div class="cost-total"><span>Estimated ${money(o.estimated_cost)}</span><strong>${o.final_cost===null?'Final cost pending':`Final ${money(o.final_cost)}`}</strong></div>${session.role!=='customer'?`<form id="order-form" data-id="${o.id}">${textarea('diagnosis','Diagnosis',o.diagnosis,'required maxlength="1000"')}${select('status','Work status',['Approved','Diagnosing','In Progress','Ready for Pickup','Completed','Cancelled'],o.status)}<button class="btn btn-primary">Update work order</button></form>`:''}`);
}
document.addEventListener('click',event=>{
 if(event.target.closest('.skip-link')){event.preventDefault();const main=document.querySelector('#main');main?.setAttribute('tabindex','-1');main?.focus();return;}
 const target=event.target.closest('[data-action]');if(!target)return;const {action,id,table:tableName}=target.dataset;
 try{
  if(action==='theme')setTheme(document.documentElement.dataset.theme==='light'?'dark':'light');
  if(action==='close')closeModal();
  if(action==='menu'){const expanded=document.querySelector('.sidebar').classList.toggle('open');target.setAttribute('aria-expanded',String(expanded));}
  if(action==='demo'){session.role=target.dataset.role;session.userId=session.role==='admin'?5:session.role==='mechanic'?4:1;navigate(session.role+'-dashboard');}
  if(action==='add'||action==='edit')editor(tableName,id);
  if(action==='delete')modal('Delete this record?',`<p>This removes the record from the current session. Records with linked history cannot be deleted.</p><div class="form-actions">${button('Keep record','close')}${button('Delete record','confirm-delete',`data-table="${tableName}" data-id="${id}"`,'danger')}</div>`);
  if(action==='confirm-delete'){remove(tableName,id);closeModal();render();toast('Record removed from this session.');}
  if(action==='request')requestDetails(id);
  if(action==='order')orderDetails(id);
  if(action==='customer'){const u=find('users',id);modal(u.full_name,`<p>${esc(u.email)} · ${esc(u.city)}</p><h3>Vehicles</h3><p>${db.vehicles.filter(v=>v.user_id===u.id).map(v=>esc(vehicleName(v.id))).join(', ')||'No vehicles yet.'}</p>`);}
  if(action==='password'){const input=document.querySelector('[name="password"]');input.type=input.type==='password'?'text':'password';target.textContent=input.type==='password'?'Show password':'Hide password';}
  if(action==='decode'||action==='demo-vin'){
   const form=document.querySelector('#entity-form');if(action==='demo-vin')form.elements.vin.value='1HGCM82633A004352';
   if(form.elements.vin.value.trim().toUpperCase()!=='1HGCM82633A004352'){toast('No demo result for this VIN. Please enter vehicle details manually.');return;}
   Object.entries({make:'Honda',model:'Accord',year:2003,fuel_type:'Petrol',transmission:'Automatic'}).forEach(([k,v])=>form.elements[k].value=v);toast('Simulated VIN preview filled. No external API was called.');
  }
  if(action==='new-ticket')modal('New support ticket',`<form id="new-ticket" class="form-grid">${field('subject','Subject','','text','required maxlength="120"')}${select('category','Category',['General','Service','Pickup','Billing'])}${select('priority','Priority',['Normal','High','Urgent'])}${session.role!=='customer'?select('customer_id','Customer',db.users.filter(u=>u.role==='customer').map(u=>[u.id,u.full_name])):''}${select('service_request_id','Related request (optional)',[['','General inquiry'],...visibleRequests().map(r=>[r.id,`SR-${r.id} · ${vehicleName(r.vehicle_id)}`])])}${textarea('description','How can we help?','','required minlength="5" maxlength="2000"')}<button class="btn btn-primary">Create ticket</button><p class="form-error full" role="alert"></p></form>`);
 }catch(error){toast(error.message);}
});
document.addEventListener('input',e=>{if(e.target.id==='search')filterResults(currentRoute().name);});
document.addEventListener('change',e=>{
 if(['filter','priority'].includes(e.target.id))filterResults(currentRoute().name);
 if(e.target.id==='role'){session.role=e.target.value;session.userId=session.role==='admin'?5:session.role==='mechanic'?4:1;navigate(session.role+'-dashboard');}
});
document.addEventListener('submit',e=>{
 const form=e.target;if(!(form instanceof HTMLFormElement))return;e.preventDefault();
 if(!form.reportValidity())return;
 const values=Object.fromEntries(new FormData(form));Object.keys(values).forEach(k=>values[k]=values[k].trim());
 const fail=message=>{const error=form.querySelector('.form-error');if(error)error.textContent=message;else toast(message);};
 try{
  if(['login-form','register-form'].includes(form.id)){
   if(form.id==='register-form'&&values.password!==values.confirm_password)return fail('Passwords must match.');
   if(form.id==='register-form'&&!values.full_name)return fail('Enter your full name.');
   session.role='customer';session.userId=1;form.reset();navigate('customer-dashboard');toast('Preview opened. No account was created or authenticated.');return;
  }
  if(form.id==='entity-form'){
   const tableName=form.dataset.table,id=Number(form.dataset.id)||undefined;
   for(const k of ['user_id','year','mileage','unit_price','quantity','minimum_stock','standard_price','estimated_duration'])if(k in values)values[k]=Number(values[k]);
   if(Object.entries(values).some(([k,v])=>!['vin'].includes(k)&&typeof v==='string'&&!v))return fail('Please complete all required fields.');
   if(tableName==='vehicles'){values.user_id=session.role==='customer'?session.userId:values.user_id;values.vin=values.vin.toUpperCase();if(id&&find('vehicles',id).user_id!==values.user_id&&db.requests.some(r=>r.vehicle_id===id))return fail('A vehicle with service history cannot be transferred in this demo.');if(db.vehicles.some(v=>v.id!==id&&(v.license_plate.toLowerCase()===values.license_plate.toLowerCase()||(values.vin&&v.vin===values.vin))))return fail('That license plate or VIN already exists.');}
   if(tableName==='users'){values.role='customer';if(db.users.some(u=>u.id!==id&&u.email.toLowerCase()===values.email.toLowerCase()))return fail('That email is already in use.');}
   if(tableName==='parts'&&db.parts.some(p=>p.id!==id&&p.sku.toLowerCase()===values.sku.toLowerCase()))return fail('That SKU is already in use.');
   save(tableName,values,id);closeModal();render();toast('Changes saved for this session.');
  }
  if(form.id==='request-form'){
   if(values.description.length<10)return fail('Please describe the issue in at least 10 characters.');
   save('requests',{...values,vehicle_id:Number(values.vehicle_id),service_id:Number(values.service_id),requested_by:session.userId,status:'Requested',created_at:new Date().toISOString().slice(0,10)});navigate('my-requests');toast('Request saved locally. No booking has been sent.');
  }
  if(form.id==='request-status'){updateRequest(form.dataset.id,values.status,Number(values.mechanic_id)||null);closeModal();render();toast('Request and work order updated.');}
  if(form.id==='order-form'){if(!values.diagnosis)return fail('Enter a diagnosis.');const o=find('orders',form.dataset.id);o.diagnosis=values.diagnosis;updateRequest(o.service_request_id,values.status);closeModal();render();toast('Work order updated.');}
  if(form.id==='new-ticket'){
   const customerId=session.role==='customer'?session.userId:Number(values.customer_id),requestId=Number(values.service_request_id)||null;
   if(requestId&&find('requests',requestId)?.requested_by!==customerId)return fail('Select a request belonging to this customer.');
   if(!values.subject||!values.description)return fail('Enter a subject and message.');
   const ticket=save('tickets',{...values,customer_id:customerId,service_request_id:requestId,status:'Open',created_at:new Date().toISOString().slice(0,10)});
   save('messages',{ticket_id:ticket.id,sender_id:session.userId,message:values.description,created_at:new Date().toISOString()});closeModal();navigate('support-ticket-details?id='+ticket.id);toast('Ticket added locally.');
  }
  if(form.id==='reply-form'){if(!values.message)return fail('Write a message first.');save('messages',{ticket_id:Number(form.dataset.id),sender_id:session.userId,message:values.message,created_at:new Date().toISOString()});render();toast('Reply added locally.');}
  if(form.id==='ticket-status'){save('tickets',{status:values.status},form.dataset.id);render();toast('Ticket updated.');}
  if(form.id==='profile-form'){if(!values.full_name||!values.city)return fail('Complete your name and city.');if(db.users.some(u=>u.id!==session.userId&&u.email.toLowerCase()===values.email.toLowerCase()))return fail('Email already in use.');save('users',values,session.userId);render();toast('Profile updated for this session.');}
 }catch(error){fail(error.message);}
});
window.addEventListener('hashchange',()=>{if(document.querySelector('#modal').open)closeModal();render();});
render();
