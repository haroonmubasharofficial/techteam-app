const money = n => new Intl.NumberFormat('en-PK', {minimumFractionDigits:2, maximumFractionDigits:2}).format(Number(n)||0);

function recalcQuotation(root) {
  let cost=0, gross=0, discount=0, net=0, tax=0;
  root.querySelectorAll('[data-item]').forEach(row => {
    const qty=+row.querySelector('[data-qty]')?.value||0;
    const pc=+row.querySelector('[data-purchase]')?.value||0;
    const dc=+row.querySelector('[data-delivery]')?.value||0;
    const oc=+row.querySelector('[data-other]')?.value||0;
    const sp=+row.querySelector('[data-selling]')?.value||0;
    const disc=Math.max(0,+row.querySelector('[data-discount]')?.value||0);
    const rate=Math.max(0,+row.querySelector('[data-tax]')?.value||0);
    const lineCost=(pc+dc+oc)*qty;
    const lineGross=sp*qty;
    const lineDiscount=Math.min(disc,lineGross);
    const lineNet=lineGross-lineDiscount;
    const lineTax=lineNet*rate/100;
    cost+=lineCost; gross+=lineGross; discount+=lineDiscount; net+=lineNet; tax+=lineTax;
    row.querySelector('[data-line-total]').textContent=money(lineNet+lineTax);
  });
  const profit=net-cost;
  const margin=net>0 ? profit/net*100 : 0;
  root.querySelector('[data-total-cost]').textContent=money(cost);
  root.querySelector('[data-selling-total]').textContent=money(gross);
  root.querySelector('[data-discount-total]').textContent=money(discount);
  root.querySelector('[data-tax-total]').textContent=money(tax);
  root.querySelector('[data-grand-total]').textContent=money(net+tax);
  root.querySelector('[data-profit]').textContent=money(profit);
  root.querySelector('[data-margin]').textContent=margin.toFixed(2)+'%';
}

function loadProduct(row) {
  const select=row.querySelector('[data-product]');
  const option=select?.selectedOptions?.[0];
  if(!option || !option.value) return;
  row.querySelector('[data-description]').value=option.dataset.description || option.dataset.name || '';
  row.querySelector('[data-unit]').value=option.dataset.unit || 'Unit';
  row.querySelector('[data-purchase]').value=option.dataset.purchase || 0;
  row.querySelector('[data-selling]').value=option.dataset.selling || 0;
  row.querySelector('[data-tax]').value=option.dataset.tax || 0;
}

function renumberRows(root) {
  root.querySelectorAll('[data-item]').forEach((row, position) => {
    row.firstElementChild.textContent=position+1;
    row.querySelectorAll('[name]').forEach(input => {
      input.name=input.name.replace(/items\[\d+\]/, `items[${position}]`);
    });
  });
}

function addRow(root) {
  const template=root.querySelector('#quotation-row-template');
  const tbody=root.querySelector('[data-items-body]');
  const index=tbody.querySelectorAll('[data-item]').length;
  if(index>=15) return;
  const html=template.innerHTML.replaceAll('__INDEX0__', index).replaceAll('__INDEX__', index+1);
  tbody.insertAdjacentHTML('beforeend', html);
  recalcQuotation(root);
}

document.addEventListener('input', e => {
  const root=e.target.closest('[data-quotation]');
  if(root) recalcQuotation(root);
});

document.addEventListener('change', e => {
  const root=e.target.closest('[data-quotation]');
  if(!root) return;
  if(e.target.matches('[data-product]')) loadProduct(e.target.closest('[data-item]'));
  recalcQuotation(root);
});

document.addEventListener('click', e => {
  if(e.target.closest('[data-add-item]')) addRow(e.target.closest('[data-quotation]'));
  if(e.target.closest('[data-remove-item]')) {
    const root=e.target.closest('[data-quotation]');
    e.target.closest('[data-item]')?.remove();
    renumberRows(root);
    recalcQuotation(root);
  }
});

document.querySelectorAll('[data-quotation]').forEach(root => {
  root.querySelectorAll('[data-product]').forEach(select => {
    if(select.value) loadProduct(select.closest('[data-item]'));
  });
  recalcQuotation(root);
});
