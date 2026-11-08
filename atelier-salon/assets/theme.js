(()=>{const toggle=document.querySelector('.menu-toggle'),nav=document.querySelector('#primary-nav');if(toggle&&nav){const close=()=>{toggle.setAttribute('aria-expanded','false');nav.classList.remove('is-open')};toggle.addEventListener('click',()=>{const open=toggle.getAttribute('aria-expanded')!=='true';toggle.setAttribute('aria-expanded',String(open));nav.classList.toggle('is-open',open)});nav.addEventListener('click',e=>{if(e.target.closest('a'))close()});document.addEventListener('keydown',e=>{if(e.key==='Escape'&&toggle.getAttribute('aria-expanded')==='true'){close();toggle.focus()}})}document.querySelectorAll('.work-filter button').forEach(b=>b.addEventListener('click',()=>{const group=b.closest('.portfolio-section');group.querySelectorAll('.work-filter button').forEach(x=>x.setAttribute('aria-pressed',String(x===b)));group.querySelectorAll('.work-card').forEach(x=>{x.hidden=b.dataset.filter!=='all'&&!x.classList.contains(b.dataset.filter)})}));})();
// Preserve editable core blocks; add service interaction only on the front end.
(()=>{
 const reduced=window.matchMedia('(prefers-reduced-motion: reduce)');
 document.querySelectorAll('.service-motion').forEach((section,sectionIndex)=>{
  const rows=[...section.querySelectorAll('.service-row')];
  const shots=[...section.querySelectorAll('.service-shot')];
  if(!rows.length||rows.length!==shots.length)return;
  const buttons=[];const details=[];
  const select=index=>{
   rows.forEach((row,i)=>{
    const active=i===index;row.classList.toggle('is-active',active);
    shots[i].classList.toggle('is-active',active);
    shots[i].setAttribute('aria-hidden',String(!active));
    buttons[i].setAttribute('aria-expanded',String(active));
    details[i].setAttribute('aria-hidden',String(!active));
    details[i].inert=!active;
    details[i].querySelectorAll('a').forEach(link=>{link.tabIndex=active?0:-1});
   });
  };
  rows.forEach((row,i)=>{
   const title=row.querySelector('h3');const detail=row.querySelector('.service-row-detail');
   const button=document.createElement('button');button.type='button';button.className='service-row-button';
   button.textContent=title.textContent;
   detail.id=`atelier-service-${sectionIndex}-${i}`;
   button.setAttribute('aria-controls',detail.id);
   const inner=document.createElement('div');inner.className='service-row-detail-inner';
   while(detail.firstChild)inner.appendChild(detail.firstChild);
   detail.appendChild(inner);title.replaceChildren(button);
   buttons.push(button);details.push(detail);
   row.style.setProperty('--row-delay',`${i*.065+.15}s`);
   button.addEventListener('click',()=>select(i));
   button.addEventListener('focus',()=>select(i));
   row.addEventListener('pointerenter',event=>{if(event.pointerType==='mouse')select(i)});
   button.addEventListener('keydown',event=>{
    let next;if(event.key==='ArrowDown')next=(i+1)%rows.length;
    else if(event.key==='ArrowUp')next=(i-1+rows.length)%rows.length;
    else if(event.key==='Home')next=0;else if(event.key==='End')next=rows.length-1;
    if(next!==undefined){event.preventDefault();buttons[next].focus()}
   });
  });
  section.classList.add('motion-ready');select(0);
  if('IntersectionObserver'in window&&!reduced.matches){
   section.classList.add('reveal-ready');
   const observer=new IntersectionObserver(entries=>{
    if(entries.some(entry=>entry.isIntersecting)){
     section.classList.add('is-in-view');observer.disconnect();
    }
   },{threshold:.08});observer.observe(section);
   reduced.addEventListener('change',event=>{if(event.matches){section.classList.add('is-in-view');observer.disconnect()}});
  }
 });
})();

(()=>{
 const motion=window.matchMedia('(prefers-reduced-motion: reduce)');
 if(!document.body.classList.contains('atelier-noir')||motion.matches||!('IntersectionObserver'in window))return;
 const elements=document.querySelectorAll('.noir-gallery-card,.noir-about-image,.noir-package');
 const observer=new IntersectionObserver(entries=>{entries.forEach(entry=>{if(entry.isIntersecting){entry.target.classList.add('is-visible');observer.unobserve(entry.target)}})},{threshold:.08});
 elements.forEach(element=>{element.classList.add('noir-reveal-ready');observer.observe(element)});
 motion.addEventListener('change',event=>{if(event.matches){elements.forEach(element=>element.classList.add('is-visible'));observer.disconnect()}});
})();

// Hosted static demo only. WordPress uses the companion plugin form instead.
document.querySelectorAll('[data-demo-booking]').forEach(group=>{
 const button=group.querySelector('[data-demo-request]');
 if(!button)return;
 const date=group.querySelector('input[type=date]');
 if(date){const now=new Date();date.min=[now.getFullYear(),String(now.getMonth()+1).padStart(2,'0'),String(now.getDate()).padStart(2,'0')].join('-')}
 button.addEventListener('click',()=>{
  for(const input of group.querySelectorAll('input,select'))if(!input.reportValidity())return;
  group.querySelector('[role=status]').hidden=false;
 });
});
