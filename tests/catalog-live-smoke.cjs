const fs=require('fs'), vm=require('vm'), assert=require('assert/strict');
class Element {
  constructor() {this.attrs={};this.handlers={};this.nodes=[];this.selectors={};this.hidden=false;this.textContent='';this.classes=new Set();this.classList={add:c=>this.classes.add(c)};}
  setAttribute(k,v){this.attrs[k]=String(v);} getAttribute(k){return this.attrs[k]??null;} removeAttribute(k){delete this.attrs[k];}
  addEventListener(k,fn){(this.handlers[k]??=[]).push(fn);} emit(k,e={}){for(const fn of this.handlers[k]||[])fn(e);}
  append(...nodes){this.nodes.push(...nodes);} before(node){this.beforeNode=node;}
  querySelector(k){return this.selectors[k]||null;} querySelectorAll(k){return k==='select'?this.selects||[]:k==='.ms-product-card'?Array(this.cardCount||0).fill({}):[];}
  contains(node){return node===this||this.nodes.includes(node);} closest(k){return this.selectors['closest:'+k]||null;}
  focus(){this.focused=true;} scrollIntoView(){this.scrolled=true;}
  set innerHTML(v){this.html=v;this.cardCount=v==='empty'?0:1;} get innerHTML(){return this.html;}
}
const catalogue=new Element(),form=new Element(),results=new Element(),apply=new Element(),label=new Element(),heading=new Element();
form.action='https://example.test/cms/products/';results.innerHTML='initial';
form.selects=['power','kv'].map(name=>{const s=new Element();s.name='mf_'+name;s.value='';s.dispatchEvent=e=>form.emit(e.type,{target:s});return s;});
form.selectors['[data-filter-apply]']=apply;catalogue.selectors['.ms-catalog-results']=results;catalogue.selectors['[data-filter-label]']=label;
const document={querySelector:k=>({'.ms-catalog':catalogue,'#ms-filter-panel form':form,'#ms-filter-panel .ms-filter-title':heading}[k]||null),createElement:()=>new Element(),handlers:{},addEventListener(k,fn){(this.handlers[k]??=[]).push(fn);}};
const timers=new Map(),requests=[],pushed=[];let serial=0;
const location={href:form.action,origin:'https://example.test'};
class FormData {constructor(f){this.f=f;}forEach(fn){this.f.selects.forEach(s=>fn(s.value,s.name));}}
class DOMParser {parseFromString(text){const data=JSON.parse(text);return {querySelector:()=>{if(data.invalid)return null;const e=new Element();e.innerHTML=data.html;e.setAttribute('data-total',data.total);return e;}};}}
const window={location,AbortController,DOMParser,history:{pushState(_,__,url){pushed.push(url);location.href=url;}},handlers:{},addEventListener(k,fn){(this.handlers[k]??=[]).push(fn);},setTimeout(fn){const id=++serial;timers.set(id,fn);return id;},clearTimeout(id){timers.delete(id);},fetch(url,opts){return new Promise((resolve,reject)=>requests.push({url,opts,resolve,reject}));}};
vm.runInNewContext(fs.readFileSync('assets/js/catalog-live.js','utf8'),{window,document,AbortController,DOMParser,FormData,URL,Event,console});
const status=results.beforeNode.nodes[0],message=status.nodes[0],retry=status.nodes[1];
const change=(i,value)=>{form.selects[i].value=value;form.emit('change',{target:form.selects[i]});};
const flush=()=>{const tasks=[...timers.values()];timers.clear();tasks.forEach(fn=>fn());};
const tick=()=>new Promise(resolve=>setImmediate(resolve));
const respond=async(i,data)=>{requests[i].resolve({ok:true,text:async()=>JSON.stringify(data)});await tick();};
(async()=>{
  assert(form.classes.has('ms-live-filters'));assert.equal(apply.type,'button');
  change(0,'under1000');change(1,'900');assert.equal(timers.size,1,'Rapid changes are debounced');flush();
  assert.equal(new URL(requests[0].url).searchParams.get('mf_kv'),'900');assert.equal(label.textContent,'Фильтры (2)');
  change(1,'1300');assert(requests[0].opts.signal.aborted,'Old request is cancelled');flush();
  await respond(1,{html:'newest',total:1});assert.equal(results.innerHTML,'newest');assert.equal(message.textContent,'Найдено товаров: 1.');
  await respond(0,{html:'outdated',total:8});assert.equal(results.innerHTML,'newest','Late stale response is ignored');assert.equal(pushed.length,1);
  location.href=form.action+'?mf_kv=900';window.handlers.popstate[0]();flush();assert.equal(form.selects[0].value,'');assert.equal(form.selects[1].value,'900');
  await respond(2,{html:'history',total:1});assert.equal(pushed.length,1,'Back restores filters without creating history entry');
  change(0,'over2000');flush();await respond(3,{invalid:true});assert.equal(results.innerHTML,'history');assert.equal(retry.hidden,false);assert.equal(results.getAttribute('aria-busy'),null);
  retry.emit('click');flush();await respond(4,{html:'empty',total:0});assert.equal(retry.hidden,true);assert.equal(message.textContent,'Найдено товаров: 0.');
  const reset=new Element();form.nodes.push(reset);reset.selectors['closest:[data-filter-reset]']=reset;
  for(const fn of document.handlers.click)fn({button:0,target:reset,preventDefault(){}});flush();
  assert(form.selects.every(s=>s.value===''));assert.equal(new URL(requests[5].url).search,'');await respond(5,{html:'all',total:12});
  const page=new Element();page.href=form.action+'?paged=2';results.nodes.push(page);page.selectors['closest:.navigation a, .pagination a, a.page-numbers']=page;
  for(const fn of document.handlers.click)fn({button:0,target:page,preventDefault(){}});flush();await respond(6,{html:'page2',total:12});assert(results.focused&&results.scrolled);
  console.log('PASS live filters: debounce, cancellation, stale responses, history, reset, failure/retry, empty results and pagination');
})().catch(e=>{console.error(e);process.exitCode=1;});
