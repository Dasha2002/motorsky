const fs = require('fs');
const vm = require('vm');
const assert = require('assert/strict');
class Element {
  constructor() {
    this.attrs = {}; this.dataset = {}; this.style = {}; this.handlers = {}; this.selectors = {}; this.parents = {};
    this.classes = new Set();
    this.classList = {add: c => this.classes.add(c), remove: c => this.classes.delete(c), contains: c => this.classes.has(c), toggle: (c, value) => value ? this.classes.add(c) : this.classes.delete(c)};
  }
  getAttribute(key) { return this.attrs[key] || null; }
  setAttribute(key, value) { this.attrs[key] = value; }
  querySelector(key) { return this.selectors[key] || null; }
  querySelectorAll() { return []; }
  closest(key) { return this.parents[key] || null; }
  addEventListener(key, fn) { (this.handlers[key] ||= []).push(fn); }
  append() {}
  focus() { this.focused = true; }
  showModal() { this.open = true; }
  close() { this.open = false; }
}
class Input extends Element {
  constructor(value = '') { super(); this.value = value; this.validityMessage = ''; }
  setCustomValidity(value) { this.validityMessage = value; }
}
class Form extends Element {
  constructor() {
    super(); this.classes.add('popup-form'); this.action = '/wp-admin/admin-ajax.php';
    this.fields = {Name: new Input('Иван'), Email: new Input('test@example.test'), Phone: new Input(''), Company: new Input('Завод'), Consent: new Input('1'), FormType: new Input('quote'), ModelId: new Input(), Model: new Input()};
    for (const [key, value] of Object.entries(this.fields)) this.selectors[`[name="${key}"]`] = value;
  }
  checkValidity() { return !this.fields.Email.validityMessage; }
  reportValidity() { this.reported = true; }
  reset() { this.didReset = true; }
}
class FormData {
  constructor(form) { this.values = new Map(form ? Object.entries(form.fields).map(([key, value]) => [key, value.value]) : []); }
  set(key, value) { this.values.set(key, value); }
  get(key) { return this.values.get(key); }
}
const body = new Element(); body.appendChild = () => {};
const listeners = {};
const dispatched = [];
const menuTrigger = new Element(), menu = new Element(), menuClose = new Element();
menu.selectors['.mobile-menu__close'] = menuClose;
const quote = new Element(), form = new Form(), success = new Element(), error = new Element(), icon = new Element(), card = new Element(), submit = new Element(), selectedModel = new Element();
success.classes.add('is-hidden');
form.parents['.popup-card'] = card;
form.selectors['[type="submit"]'] = submit;
form.selectors['.popup-form__error'] = error;
form.selectors['[data-selected-model]'] = selectedModel;
card.selectors['.popup-icon'] = icon; card.selectors['.popup-success'] = success;
quote.selectors['.popup-form'] = form; quote.selectors['.popup-success'] = success; quote.selectors['.popup-icon'] = icon;
quote.selectors['input:not([type="hidden"]):not([tabindex="-1"]), textarea'] = form.fields.Name;
const quoteTrigger = new Element();
quoteTrigger.attrs = {'data-popup': 'quote', 'data-product-id': '9', 'data-product-name': 'STAR 3115 — 900 KV'};
quoteTrigger.parents['[data-popup]'] = quoteTrigger;
const selectors = {'[data-mobile-menu-open]': menuTrigger, '.mobile-menu': menu, '.popup-overlay[data-popup-id="quote"]': quote};
const document = {
  body, documentElement: {style:{}}, readyState: 'complete',
  querySelector: key => key === '.popup-overlay.is-open, .mobile-menu.is-open' ? (quote.classes.has('is-open') ? quote : menu.classes.has('is-open') ? menu : null) : key === '.mobile-menu.is-open' ? (menu.classes.has('is-open') ? menu : null) : selectors[key] || null,
  querySelectorAll: key => key === '.popup-overlay.is-open' && quote.classes.has('is-open') ? [quote] : [],
  addEventListener: (key, fn) => (listeners[key] ||= []).push(fn),
  dispatchEvent: event => { dispatched.push(event); for (const fn of listeners[event.type] || []) fn(event); },
  createElement: () => new Element()
};
const requests = [];
let sendSuccess = true;
const window = {setTimeout: fn => fn(), matchMedia: () => ({matches: false}), addEventListener() {}};
const context = {window, document, Element, HTMLFormElement: Form, HTMLInputElement: Input, FormData, CustomEvent: class {constructor(type, args) {this.type = type; this.detail = args.detail;}}, console,
  fetch: async (url, options) => {
    requests.push(options.body);
    return {json: async () => options.body.get('action') === 'motorsky_lead_nonce' ? {success: true, data: {nonce: 'fresh'}} : sendSuccess ? {success: true} : {success: false, data: {message: 'SMTP недоступен'}}};
  }
};
vm.runInNewContext(fs.readFileSync('assets/js/script.js', 'utf8'), context);
const phoneInput = form.fields.Phone;
phoneInput.parents['.popup-form__field--phone'] = new Element();
phoneInput.parents['.popup-form'] = form;
const phoneEvent = type => { for (const fn of listeners[type] || []) fn({target:phoneInput}); };
phoneEvent('focusin'); assert.equal(phoneInput.value, '+7 ', 'Country prefix is inserted on focus');
for (const [raw, expected] of [['9991234567','+7 (999) 123-45-67'],['89991234567','+7 (999) 123-45-67'],['+7 (999) 123-45-67','+7 (999) 123-45-67'],['7991234567','+7 (799) 123-45-67'],['8991234567','+7 (899) 123-45-67']]) {
  phoneInput.value=raw; phoneEvent('input'); assert.equal(phoneInput.value,expected,'Phone formatting: '+raw);
}
phoneInput.value='+7 '; phoneEvent('blur'); assert.equal(phoneInput.value,'','An unused phone stays optional');
const click = target => { for (const fn of listeners.click || []) fn({target, preventDefault() {}}); };
const send = () => { for (const fn of listeners.submit || []) fn({target: form, preventDefault() {}}); };
const tick = () => new Promise(resolve => setImmediate(resolve));
(async () => {
  body.style.overflow = 'auto'; document.documentElement.style.overflow = 'scroll';
  menuTrigger.handlers.click[0]({preventDefault() {}});
  assert(menu.classes.has('is-open'), 'Burger opens without form variables');
  assert.equal(body.style.overflow, 'hidden'); assert.equal(document.documentElement.style.overflow, 'hidden');
  menuClose.handlers.click[0]();
  assert.equal(body.style.overflow, 'auto'); assert.equal(document.documentElement.style.overflow, 'scroll');
  click(quoteTrigger);
  assert.equal(document.documentElement.style.overflow, 'hidden'); assert.equal(quote.scrollTop, 0);
  menuTrigger.handlers.click[0]({preventDefault() {}}); menuClose.handlers.click[0]();
  assert.equal(document.documentElement.style.overflow, 'hidden', 'Closing menu keeps open popup background locked');
  assert.equal(form.fields.ModelId.value, '9'); assert.equal(form.fields.Model.value, 'STAR 3115 — 900 KV');
  assert.equal(selectedModel.textContent, 'STAR 3115 — 900 KV'); assert(form.fields.Name.focused);
  form.fields.Email.value = ''; form.fields.Phone.value = '+7 ';
  send(); assert(form.reported); assert.equal(requests.length, 0, 'Empty contacts do not submit');
  form.fields.Email.value = 'test@example.test';
  send(); for (let i = 0; i < 5; i++) await tick();
  assert.equal(requests.length, 2); assert.equal(requests[1].get('motorsky_nonce'), 'fresh');
  assert.equal(form.style.display, 'none'); assert(!success.classes.has('is-hidden'));
  assert.equal(dispatched.filter(e => e.type === 'motorsky:lead-sent').length, 1);
  click(quoteTrigger); assert.equal(form.style.display, ''); assert(success.classes.has('is-hidden'));
  sendSuccess = false; send(); for (let i = 0; i < 5; i++) await tick();
  assert.equal(error.textContent, 'SMTP недоступен'); assert.equal(error.hidden, false); assert.equal(form.style.display, ''); assert.equal(submit.disabled, false);
  assert.equal(dispatched.filter(e => e.type === 'motorsky:lead-sent').length, 1, 'Failed email does not emit success analytics');
  for (const fn of listeners.keydown || []) fn({key:'Escape'});
  assert.equal(body.style.overflow, 'auto'); assert.equal(document.documentElement.style.overflow, 'scroll');
  assert(quoteTrigger.focused, 'Close restores focus without scrolling the background');
  console.log('PASS burger, quote model, honeypot-safe focus, contact validation, fresh nonce, success, retry, SMTP error');
  const events = [];
  window.gtag = (...args) => events.push(args);
  vm.runInNewContext(fs.readFileSync('assets/js/catalog.js', 'utf8'), context);
  const cta = new Element(); cta.dataset = {msEvent: 'quote_open', productId: '9'}; cta.parents['[data-ms-event]'] = cta;
  click(cta);
  document.dispatchEvent({type: 'motorsky:lead-sent', detail: {formType: 'selection', productId: ''}});
  assert.equal(events[0][1], 'motorsky_quote_open'); assert.equal(events[1][1], 'motorsky_form_selection_sent');
  assert(!JSON.stringify(events).includes('test@example.test'), 'No contact details in analytics');
  console.log('PASS analytics CTA/form events without contact data');
})().catch(error => { console.error(error); process.exitCode = 1; });
