import test from 'node:test';
import assert from 'node:assert/strict';
import {readFileSync,existsSync} from 'node:fs';
import {seed} from '../frontend/js/mockData.js';
import {db,save,remove,updateRequest} from '../frontend/js/services/store.js';
import {routes} from '../frontend/js/router.js';
import {esc} from '../frontend/js/components/ui.js';
test('Every route has a standalone partial without duplicate document/script tags',()=>{
 for(const route of Object.keys(routes)){
  const html=readFileSync(new URL(`../frontend/views/${route}.html`,import.meta.url),'utf8');
  assert.ok(html.trim().length>80,route);assert.doesNotMatch(html,/<(?:html|body|script)\b/i);
 }
 for(const asset of ['css/styles.css','css/components.css','css/responsive.css','assets/vendor/bootstrap.min.css','assets/images/vehicle.svg'])assert.ok(existsSync(new URL('../frontend/'+asset,import.meta.url)));
});
test('Mock foreign keys, owners, order uniqueness and item XOR are consistent',()=>{
 const has=(table,id)=>seed[table].some(r=>r.id===id);
 for(const v of seed.vehicles)assert.ok(has('users',v.user_id));
 for(const r of seed.requests){assert.ok(has('services',r.service_id));assert.equal(seed.vehicles.find(v=>v.id===r.vehicle_id)?.user_id,r.requested_by);}
 assert.equal(new Set(seed.orders.map(o=>o.service_request_id)).size,seed.orders.length);
 for(const o of seed.orders){assert.ok(has('requests',o.service_request_id));assert.equal(seed.users.find(u=>u.id===o.mechanic_id)?.role,'mechanic');assert.ok(seed.items.some(i=>i.service_order_id===o.id));}
 for(const i of seed.items){assert.ok(has('orders',i.service_order_id));assert.notEqual(i.service_id===null,i.part_id===null);assert.ok(has(i.service_id?'services':'parts',i.service_id||i.part_id));assert.ok(i.quantity>0);}
 for(const t of seed.tickets){assert.ok(has('users',t.customer_id));if(t.service_request_id)assert.equal(seed.requests.find(r=>r.id===t.service_request_id)?.requested_by,t.customer_id);}
 for(const m of seed.messages){assert.ok(has('tickets',m.ticket_id));assert.ok(has('users',m.sender_id));}
});
test('Approval creates one order, completion synchronizes status and final cost',()=>{
 const original=db.orders.length;
 updateRequest(2,'Approved',4);updateRequest(2,'In Progress',4);
 assert.equal(db.orders.length,original+1);
 const order=db.orders.find(o=>o.service_request_id===2);
 assert.equal(order.mechanic_id,4);
 updateRequest(2,'Completed');assert.equal(order.final_cost,60);assert.equal(db.requests.find(r=>r.id===2).status,'Completed');
 updateRequest(2,'Diagnosing');assert.equal(order.final_cost,null);
});
test('Referenced data cannot be deleted, independent demo records can',()=>{
 assert.throws(()=>remove('vehicles',1),/linked history/);
 assert.throws(()=>remove('parts',1),/linked history/);
 const part=save('parts',{name:'Test part',sku:'TEST',quantity:1});
 remove('parts',part.id);assert.equal(db.parts.some(p=>p.id===part.id),false);
});
test('User text is HTML escaped',()=>assert.equal(esc('<img src=x onerror="alert(1)">'), '&lt;img src=x onerror=&quot;alert(1)&quot;&gt;'));
