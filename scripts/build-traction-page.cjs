// Keep the original servo markup intact; only substitute editable page content.
const fs = require('node:fs');
const path = require('node:path');
const root = path.resolve(__dirname, '..');
const read = file => fs.readFileSync(path.join(root, file), 'utf8');
const text = key => `<?php motorsky_traction_text('${key}'); ?>`;
const url = key => `<?php motorsky_traction_url('${key}'); ?>`;
const attr = key => `<?php motorsky_traction_attr('${key}'); ?>`;
const imageTag = /<img\b(?:[^>"']|"[^"]*"|'[^']*')*>/g;
const write = (name, html) => {
  // An intentionally cleared image does not request the page URL as an image.
  html = html.replace(imageTag, tag => {
    const key = tag.match(/motorsky_traction_url\('([^']+)'\)/)?.[1];
    return key ? `<?php if (motorsky_traction_value(get_the_ID(), '${key}') !== '') : ?>${tag}<?php endif; ?>` : tag;
  });
  fs.mkdirSync(path.join(root, 'includes/traction-page'), {recursive:true});
  fs.writeFileSync(path.join(root, 'includes/traction-page', name+'.php'), html);
};
let hero = read('includes/product-servo-page/hero-servo.php');
hero = hero.replace(imageTag, (tag, offset) => tag.includes('hero_gradient')
  ? `<img src="${url('hero_glow')}" alt="">`
  : `<img src="${url('hero_image')}" alt="${attr('hero_alt')}">`);
hero = hero.replace(/(<h1[^>]*>)[^]*?(<\/h1>)/, `$1<span class="blue-heading">${text('hero_accent')}</span> ${text('hero_title')}$2`)
  .replace(/(<p class="shero__desc">)[^]*?(<\/p>)/, `$1${text('hero_description')}$2`)
  .replace('>Связаться</button>', `>${text('hero_button')}</button>`);
write('hero',hero);
let info = read('includes/product-servo-page/characteristic-servo.php');
let heading=0, feature=0, title=0, description=0, image=0;
info = info.replace(/(<h2\b[^>]*>)[^]*?(<\/h2>)/g, (_,a,b) => a+text(heading++ ? 'advantages_title':'overview_title')+b)
  .replace(/<p>([^]*?)<\/p>/g, () => `<p>${text(++feature <= 6 ? 'feature_'+feature : 'advantage_'+(feature-6))}</p>`)
  .replace('aria-label="Типы серводвигателей"', `aria-label="${attr('types_label')}"`)
  .replace(imageTag, () => {image++; return `<img class="servo-types__image" src="${url('type_'+image+'_image')}" alt="${attr('type_'+image+'_alt')}" loading="lazy">`;})
  .replace(/(<h3\b[^>]*>)[^]*?(<\/h3>)/g, (_,a,b) => a+text('type_'+(++title)+'_title')+b)
  .replace(/(<p class="servo-types__description">)[^]*?(<\/p>)/g, (_,a,b) => a+text('type_'+(++description)+'_description')+b)
  .replace(/(<p class="servo-info__description">)[^]*?(<\/p>)/, `$1${text('summary')}$2`);
write('characteristic',info);
let application = read('includes/product-page/application.php');
image=0; title=0;
application = application.replace(/(<h2[^>]*>)[^]*?(<\/h2>)/, `$1${text('application_title')}$2`)
  .replace(imageTag, () => {image++;return `<img src="${url('application_'+image+'_image')}" alt="${attr('application_'+image+'_alt')}">`;})
  .replace(/(<h3[^>]*>)[^]*?(<\/h3>)/g, (_,a,b) => a+text('application_'+(++title)+'_title')+b);
write('application',application);
let contacts=read('includes/contacts.php')
  .replace('<h2>Адрес</h2>', `<h2>${text('contact_title')}</h2>`)
  .replace('<p>г . Пермь</p>', `<p>${text('contact_address')}</p>`)
  .replace(/src="[^"]*"/, `src="${url('contact_map')}"`)
  .replace(/title="[^"]*"/, `title="${attr('contact_map_title')}"`);
contacts=contacts.replace(/<iframe[^]*?<\/iframe>/, tag => `<?php if (motorsky_traction_value(get_the_ID(), 'contact_map') !== '') : ?>${tag}<?php endif; ?>`);
write('contacts',contacts);
for(const file of ['includes/header.php','includes/footer.php']) {
  let html=read(file).replace(/motorsky_page_url\( 'product-servo-page.php', 'servo' \)([^\n]*Тяговые)/g, "motorsky_page_url( 'traction-page.php', 'traction' )$1");
  fs.writeFileSync(path.join(root,file),html);
}
let directions=read('includes/main-page/directions.php');
let link=0;
directions=directions.replace(/motorsky_page_url\( 'product-servo-page.php', 'servo' \)/g, match => ++link===2 ? "motorsky_page_url( 'traction-page.php', 'traction' )" : match);
fs.writeFileSync(path.join(root,'includes/main-page/directions.php'),directions);
