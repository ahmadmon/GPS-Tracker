<div x-data="{ status: @js($status),
               url: @js($url),
               key: null,

               init(){
                    this.key = this.url.charAt(this.url.length - 1);
               },

               async handleStatus(){
                    const response = await fetch(this.url);
                    const data = await response.json();

                    if(data.status){
                        this.status = data.data;
                    }
               }
 }">
    <ul class="tg-list common-flex">
        <li class="tg-list-item cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top"
            data-bs-title="کلیک برای تغییر وضعیت">
            <input class="tgl tgl-flip" @change="handleStatus()" :id="`cb-${key}`" type="checkbox" :checked="status">
            <label class="tgl-btn" data-tg-off="غیرفعال" data-tg-on="فعال" :for="`cb-${key}`"></label>
        </li>
    </ul>
</div>
