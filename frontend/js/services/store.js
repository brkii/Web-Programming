import {seed} from '../mockData.js';
// The only data-access boundary. Replace with asynchronous API services in M4.
export const db=structuredClone(seed);
export const session={role:'customer',userId:1};
export const find=(table,id)=>db[table].find(row=>row.id===Number(id));
export function save(table,values,id){
 if(id){const row=find(table,id); if(!row)throw Error('Record no longer exists.'); Object.assign(row,values); return row;}
 const row={...values,id:Math.max(0,...db[table].map(r=>r.id))+1};db[table].push(row);return row;
}
export function remove(table,id){
 const dependencies={users:[['vehicles','user_id'],['requests','requested_by'],['orders','mechanic_id'],['tickets','customer_id'],['messages','sender_id']],vehicles:[['requests','vehicle_id']],services:[['requests','service_id'],['items','service_id']],parts:[['items','part_id']]};
 if((dependencies[table]||[]).some(([t,k])=>db[t].some(r=>r[k]===Number(id))))throw Error('This record has linked history. Keep it to preserve service records.');
 db[table]=db[table].filter(r=>r.id!==Number(id));
}
export const customerVehicles=()=>db.vehicles.filter(v=>v.user_id===session.userId);
export const visibleRequests=()=>session.role==='customer'?db.requests.filter(r=>r.requested_by===session.userId):db.requests;
export const visibleTickets=()=>session.role==='customer'?db.tickets.filter(t=>t.customer_id===session.userId):db.tickets;
export const money=value=>new Intl.NumberFormat('en-BA',{style:'currency',currency:'BAM'}).format(value||0);
export function updateRequest(id,status,mechanicId){
 const request=find('requests',id); request.status=status;
 let order=db.orders.find(o=>o.service_request_id===request.id);
 if(!order&&!['Requested','Cancelled'].includes(status)){
  const service=find('services',request.service_id);
  order=save('orders',{service_request_id:request.id,mechanic_id:mechanicId||null,diagnosis:'Awaiting workshop assessment.',status,estimated_cost:service.standard_price,final_cost:null,created_at:new Date().toISOString().slice(0,10)});
  save('items',{service_order_id:order.id,service_id:service.id,part_id:null,quantity:1,unit_price:service.standard_price});
 }
 if(order){order.status=status;if(mechanicId!==undefined)order.mechanic_id=mechanicId;if(status==='Completed')order.final_cost=db.items.filter(i=>i.service_order_id===order.id).reduce((sum,i)=>sum+i.quantity*i.unit_price,0);else order.final_cost=null;}
}
