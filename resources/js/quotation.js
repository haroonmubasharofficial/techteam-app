const money = n => new Intl.NumberFormat('en-PK', {minimumFractionDigits:2, maximumFractionDigits:2}).format(Number(n)||0);
function recalcQuotation(root) {
  let cost=0, net=0, tax=0;
  root.querySelectorAll('[data-item]').forEach(row => {
    const qty=+row.querySelector('[data-qty]')?.value||0;
    const pc=+row.querySelector('[data-purchase]')?.value||0;
    const dc=+row.querySelector('[data-delivery]')?.value||0;
    const oc=+row.querySelector('[data-other]')?.value||0;
    const sp=+row.querySelector('[data-selling]')?.value||0;
    const disc=+row.querySelector('[data-discount]')?.value||0;
    const rate=+row.querySelector('[data-tax]')?.value||0;
    const lineCost=(pc+dc+oc)*qty;
    const lineNet=Math.max(0,sp*qty-disc);
    const lineTax=lineNet*rate/100;
    cost+=lineCost; net+=lineNet; tax+=lineTax;
    row.querySelector('[data-line-total]').textContent=money(lineNet+lineTax);
  });
  const profit=net-cost;
  const margin=net>0 ? profit/net*100 : 0;
  root.querySelector('[data-total-cost]').textContent=money(cost);
  root.querySelector('[data-selling-total]').textContent=money(net);
  root.querySelector('[data-tax-total]').textContent=money(tax);
  root.querySelector('[data-grand-total]').textContent=money(net+tax);
  root.querySelector('[data-profit]').textContent=money(profit);
  root.querySelector('[data-margin]').textContent=margin.toFixed(2)+'%';
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
document.addEventListener('input', e => { const root=e.target.closest('[data-quotation]'); if(root) recalcQuotation(root); });
document.addEventListener('click', e => {
  if(e.target.closest('[data-add-item]')) addRow(e.target.closest('[data-quotation]'));
  if(e.target.closest('[data-remove-item]')) { e.target.closest('[data-item]')?.remove(); recalcQuotation(e.target.closest('[data-quotation]')); }
});
document.querySelectorAll('[data-quotation]').forEach(recalcQuotation);
