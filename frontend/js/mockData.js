export const seed = {
 users: [
  {id:1,full_name:'Amina Brković',email:'amina@example.test',role:'customer',city:'Sarajevo'},
  {id:2,full_name:'Amir Delić',email:'amir@example.test',role:'customer',city:'Mostar'},
  {id:3,full_name:'Sara Kovač',email:'sara@example.test',role:'customer',city:'Tuzla'},
  {id:4,full_name:'Emin Bešić',email:'emin@example.test',role:'mechanic',city:'Sarajevo'},
  {id:5,full_name:'Nina Marić',email:'nina@example.test',role:'admin',city:'Sarajevo'}
 ],
 vehicles:[
  {id:1,user_id:1,make:'Volvo',model:'XC60',year:2022,license_plate:'A12-K-345',mileage:42850,fuel_type:'Hybrid',transmission:'Automatic',vin:'',color:'blue'},
  {id:2,user_id:1,make:'Volkswagen',model:'Golf VIII',year:2021,license_plate:'E23-M-678',mileage:67200,fuel_type:'Diesel',transmission:'Manual',vin:'',color:'silver'},
  {id:3,user_id:2,make:'BMW',model:'320d',year:2020,license_plate:'J45-A-901',mileage:89200,fuel_type:'Diesel',transmission:'Automatic',vin:'',color:'blue'},
  {id:4,user_id:3,make:'Audi',model:'A3',year:2023,license_plate:'K67-T-234',mileage:21600,fuel_type:'Petrol',transmission:'Automatic',vin:'',color:'silver'}
 ],
 services:[{id:1,name:'Annual maintenance',description:'Oil, filters and a full vehicle inspection.',standard_price:120,estimated_duration:90},{id:2,name:'Brake inspection',description:'Inspect pads, discs and brake fluid.',standard_price:60,estimated_duration:45},{id:3,name:'Engine diagnostics',description:'Fault-code scan and diagnostic report.',standard_price:80,estimated_duration:60},{id:4,name:'Tyre change',description:'Seasonal tyre fitting and wheel balancing.',standard_price:50,estimated_duration:40}],
 requests:[
  {id:1,vehicle_id:1,requested_by:1,service_id:1,description:'Annual service and oil replacement.',preferred_date:'2026-11-03',priority:'Normal',status:'In Progress',notes:'Please check wipers.',created_at:'2026-10-28'},
  {id:2,vehicle_id:2,requested_by:1,service_id:2,description:'Squeaking when braking at low speed.',preferred_date:'2026-11-05',priority:'High',status:'Requested',notes:'',created_at:'2026-10-30'},
  {id:3,vehicle_id:3,requested_by:2,service_id:3,description:'Engine warning light is on.',preferred_date:'2026-11-02',priority:'Urgent',status:'Diagnosing',notes:'',created_at:'2026-10-29'},
  {id:4,vehicle_id:4,requested_by:3,service_id:4,description:'Fit winter tyres.',preferred_date:'2026-10-30',priority:'Normal',status:'Ready for Pickup',notes:'',created_at:'2026-10-27'},
  {id:5,vehicle_id:2,requested_by:1,service_id:1,description:'Scheduled maintenance.',preferred_date:'2026-09-15',priority:'Normal',status:'Completed',notes:'',created_at:'2026-09-10'}
 ],
 orders:[{id:1,service_request_id:1,mechanic_id:4,diagnosis:'Routine service. Replace oil and filter.',status:'In Progress',estimated_cost:185,final_cost:null,created_at:'2026-11-01'}, {id:2,service_request_id:3,mechanic_id:4,diagnosis:'Inspect intake sensor and wiring.',status:'Diagnosing',estimated_cost:80,final_cost:null,created_at:'2026-11-01'}, {id:3,service_request_id:4,mechanic_id:4,diagnosis:'Winter tyres fitted and balanced.',status:'Ready for Pickup',estimated_cost:50,final_cost:null,created_at:'2026-10-30'}, {id:4,service_request_id:5,mechanic_id:4,diagnosis:'Oil and filter replaced. Inspection passed.',status:'Completed',estimated_cost:185,final_cost:185,created_at:'2026-09-15'}],
 parts:[{id:1,name:'Engine oil 5W-30 (1 L)',sku:'OIL-530',category:'Lubricants',unit_price:12,quantity:24,minimum_stock:10},{id:2,name:'Oil filter',sku:'FLT-001',category:'Filters',unit_price:17,quantity:3,minimum_stock:5},{id:3,name:'Front brake pads',sku:'BRK-204',category:'Brakes',unit_price:65,quantity:8,minimum_stock:4}],
 items:[{id:1,service_order_id:1,service_id:1,part_id:null,quantity:1,unit_price:120},{id:2,service_order_id:1,service_id:null,part_id:1,quantity:4,unit_price:12},{id:3,service_order_id:1,service_id:null,part_id:2,quantity:1,unit_price:17},{id:4,service_order_id:2,service_id:3,part_id:null,quantity:1,unit_price:80},{id:5,service_order_id:3,service_id:4,part_id:null,quantity:1,unit_price:50},{id:6,service_order_id:4,service_id:1,part_id:null,quantity:1,unit_price:120},{id:7,service_order_id:4,service_id:null,part_id:1,quantity:4,unit_price:12},{id:8,service_order_id:4,service_id:null,part_id:2,quantity:1,unit_price:17}],
 tickets:[{id:1,customer_id:1,service_request_id:1,subject:'Can I collect my car after 17:00?',description:'I finish work at 17:00. Is a later pickup possible?',category:'Pickup',priority:'Normal',status:'Open',created_at:'2026-11-01'},{id:2,customer_id:2,service_request_id:3,subject:'Diagnostic estimate',description:'Please confirm the estimate before further work.',category:'Billing',priority:'High',status:'Waiting for Customer',created_at:'2026-10-31'}],
 messages:[{id:1,ticket_id:1,sender_id:1,message:'I finish work at 17:00. Is a later pickup possible?',created_at:'2026-11-01T09:15:00'},{id:2,ticket_id:1,sender_id:5,message:'Of course, Amina. We are open until 18:00. We will let you know when your Volvo is ready.',created_at:'2026-11-01T09:24:00'},{id:3,ticket_id:2,sender_id:2,message:'Please confirm the estimate before further work.',created_at:'2026-10-31T12:00:00'}]
};
export const requestStatuses=['Requested','Approved','Diagnosing','In Progress','Ready for Pickup','Completed','Cancelled'];
export const ticketStatuses=['Open','In Progress','Waiting for Customer','Resolved','Closed'];
