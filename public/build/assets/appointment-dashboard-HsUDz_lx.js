let u=$(".on-off-btn");u.each(function(){$(this).on("click",function(){$(this).toggleClass(["btn-outline-gray-800","btn-outline-gray-500"]),$(this).hasClass("btn-outline-gray-800")?$(this).text("{{ __('turn off ') }}"):$(this).text("{{ __('turn on ') }}")})});function f(t,a=null){let n={...{day:null,count:0,removeBtn:!1},...t},l=Math.random().toString(36).substring(2)+Math.random().toString(36).substring(2),o={id:"",name:n.day,count:n.count};a!=null&&(o.time=a);let s=`<button onclick="$('#${l}').remove()" class="btn btn-sm text-secondary px-0 ps-sm-2 mt-2 mt-sm-0 ms-sm-1 col-sm-2" type="button">Remove<span class="d-sm-none"> time above</span> </button>`;return`<div class="mt-minus-first mt-3" id="${l}">
            <div class="d-flex align-items-center flex-wrap flex-sm-nowrap col">
              ${d(o)}
              ${n.removeBtn?s:""}
            </div>
          </div>`}function p(t=null){let a=Math.random().toString(36).substring(2)+Math.random().toString(36).substring(2),e=$("#excluded-dates-box"),n="excluded_days",l=e.find(".excluded-date").length,o={id:"",name:n,count:l},s;t!=null&&(s=t.day,o.time=h(t.from_date,t.to_date));let r=`
  <div class="p-3 border-bottom excluded-date" id="${a}">
    <div class="d-flex align-items-center flex-wrap col">
      <input class="form-control me-md-2 mb-3 mb-md-0 col-12 col-md" type="date" name="${n}[times][${l}][day]" ${t!=null?`value="${s}"`:""}>
      <div class="d-flex flex-wrap flex-md-nowrap align-items-center">
        ${d(o)}
        <div class="col-12 col-md mt-2 mt-md-0 ms-0 ms-md-3 text-end">
          <button onclick="$('#${a}').remove()" class="btn btn-sm btn-link link-danger w-auto p-0" type="button">Remove</button>
        </div>
      </div>
    </div>
  </div>`;$("#excluded-dates-box div:last").before(r)}function i(t=0,a=60,e=!0){let n="";for(let l=e?0:1;l<a;l++)n=n+`<option value="${l<10?"0"+l:l}" ${l==t?"selected":""}>
        ${l<10?"0"+l:l}
      </option>`;return n}function d(t){let e={...{id:"no-id-given",name:null,count:0,time:{from_hour:"09",from_minute:"00",from_format:"am",to_hour:"06",to_minute:"00",to_format:"pm"}},...t},n=e.time;return`<div class="d-flex align-items-center" id="${e.id}">
          <div class="input-group" style="min-width: 160px;">
          <select class="form-control" ${e.name!=null&&`name="${e.name}[times][${e.count}][from_hour]"`}>
              ${i(n.fromHour,12,!1)}
            </select>
            <span class="input-group-text bg-white" style="padding: 0px 3px;">:</span>
            <select class="form-control" ${e.name!=null&&`name="${e.name}[times][${e.count}][from_minut]"`}>
              ${i(n.fromMinute,60)}
            </select>
            <select class="form-control" ${e.name!=null&&`name="${e.name}[times][${e.count}][from_format]"`}>
              <option ${n.fromFormat=="am"&&"selected"} value="am">AM</option>
              <option ${n.fromFormat=="pm"&&"selected"} value="pm">PM</option>
            </select>
          </div>
          <div class="mx-2 text-secondary small">-</div>
          <div class="input-group" style="min-width: 160px;">
          <select class="form-control" ${e.name!=null&&`name="${e.name}[times][${e.count}][to_hour]"`}>
              ${i(n.toHour,12,!1)}
          </select>
          <span class="input-group-text bg-white" style="padding: 0px 3px;">:</span>
          <select class="form-control" ${e.name!=null&&`name="${e.name}[times][${e.count}][to_minut]"`}>
              ${i(n.toMinute,60)}
            </select>
            <select class="form-control" ${e.name!=null&&`name="${e.name}[times][${e.count}][to_format]"`}>
              <option ${n.toFormat=="am"&&"selected"} value="am">AM</option>
              <option ${n.toFormat=="pm"&&"selected"} value="pm">PM</option>
            </select>
          </div>
        </div>`}function m(t){let a=t.parents(".day-element");t.prop("checked")?(a.find(".disable-if-no-day").removeClass("d-none").addClass("d-flex"),a.find(".unavailable").addClass("d-none").removeClass("d-block")):(a.find(".disable-if-no-day").addClass("d-none").removeClass("d-flex"),a.find(".unavailable").removeClass("d-none").addClass("d-block"))}function v(t,a=null){let e=`available_days[${t.data("day")}]`,n=t.find(".time-windows").children().length;t.find(".time-windows").append(f({day:e,count:n,removeBtn:n>0},a))}function h(t,a){t=t.split("-")[0],a=a.split("-")[0];let e=t;t.search("T")>-1&&(e=t.split("T")[1]);let n=e.split(":"),l=a;a.search("T")>-1&&(l=a.split("T")[1]);let o=l.split(":");return{fromHour:n[0],fromMinute:n[1],fromFormat:n[0]>11?"pm":"am",toHour:o[0],toMinute:o[1],toFormat:o[0]>11?"pm":"am"}}$(".add-window-btn").each(function(){$(this).on("click",function(){let t=$(this).parents(".day-element");v(t)})});$(".day-available-check").each(function(){m($(this)),$(this).on("input",function(){m($(this))})});function c(){$("#overrides").prop("checked")?($("#excluded-dates").removeClass("d-none"),$("#excluded-dates [name]").each(function(){$(this).attr("name",$(this).data("name"))})):($("#excluded-dates").addClass("d-none"),$("#excluded-dates [name]").each(function(){$(this).data("name",$(this).attr("name")),$(this).attr("name","")}))}$("#overrides").on("input",c);c();$("#add-excluded-date-btn").on("click",function(){p()});
