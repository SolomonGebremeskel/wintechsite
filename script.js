const values = [

{
icon:"🛡️",
title:"Professional Support",
description:"Our experienced team is always ready to provide fast and reliable technical support."
},

{
icon:"💰",
title:"Affordable Solutions",
description:"High-quality IT services that fit your budget without compromising quality."
},

{
icon:"⚡",
title:"Fast Project Delivery",
description:"Projects are completed on time with efficiency and attention to detail."
},

{
icon:"🔒",
title:"Secure Technology",
description:"We implement secure systems to protect your business and data."
},

{
icon:"📈",
title:"Scalable Systems",
description:"Solutions designed to grow together with your business."
},

{
icon:"🤝",
title:"Customer Focused",
description:"Your success is our priority. We build lasting relationships through excellent service."
}

];

let current = 0;

const icon = document.getElementById("icon");
const title = document.getElementById("title");
const description = document.getElementById("description");
const card = document.getElementById("valueCard");
const dots = document.querySelectorAll(".dot");

function changeCard(){

card.classList.add("fade");

setTimeout(()=>{

current++;

if(current>=values.length){
current=0;
}

icon.innerHTML=values[current].icon;
title.innerHTML=values[current].title;
description.innerHTML=values[current].description;

dots.forEach(dot=>dot.classList.remove("active"));
dots[current].classList.add("active");

card.classList.remove("fade");

},300);

}

setInterval(changeCard,2500);