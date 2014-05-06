function ServerStatus(){
  $.getJSON('/status.json', function(serverStats) {
    var RamUsedP = serverStats.ram_u/serverStats.ram_t*100;
    var RamFreeP = serverStats.ram_f/serverStats.ram_t*100;
    var RamCacheP = (serverStats.ram_ub-serverStats.ram_u)/serverStats.ram_t*100;
    
    /* Cek kalo uptime menit = min (hour = 0) error */
    if (serverStats.up_m=="min") {
        serverStats.up_m=serverStats.up_h;
        serverStats.up_h=0;
    }
    
    document.getElementById("ServerStatsOs").innerHTML=serverStats.os;
    $("#ServerStatsOs_Link").attr("href", serverStats.os_link);
    document.getElementById("ServerStatsKernel").innerHTML=serverStats.kernel;
    document.getElementById("ServerStatsProc").innerHTML=serverStats.proc;
    document.getElementById("ServerStatsCore").innerHTML=serverStats.core;
    document.getElementById("ServerStatsUl").innerHTML=serverStats.ul;
    document.getElementById("ServerStatsDl").innerHTML=serverStats.dl;
    document.getElementById("ServerStatsUp_D").innerHTML=serverStats.up_d+' Days';
    document.getElementById("ServerStatsUp_H").innerHTML=serverStats.up_h+' Hours';
    document.getElementById("ServerStatsUp_M").innerHTML=serverStats.up_m+' Minutes';
    document.getElementById("ServerStatsRam_U").innerHTML=RamUsedP.toFixed(2)+'% Used';
    document.getElementById("ServerStatsRam_C").innerHTML=RamCacheP.toFixed(2)+'% Cache';
    document.getElementById("ServerStatsRam_F").innerHTML=RamFreeP.toFixed(2)+'% Free';
    $("#ServerStatsRamUsedP").attr("style", 'width: '+RamUsedP.toFixed(2)+'%');
    $("#ServerStatsRamCacheP").attr("style", 'width: '+RamCacheP.toFixed(2)+'%');
    document.getElementById("ServerStatsDiskU").innerHTML=serverStats.disk_p+'% ('+serverStats.disk_u+'GB) Used';
    document.getElementById("ServerStatsDiskF").innerHTML=(100-serverStats.disk_p)+'% ('+serverStats.disk_f+'GB) Free';
    $("#ServerStatsDiskP").attr("style", 'width: '+serverStats.disk_p+'%');
    
    /* Multicolor progressbar */
    if (RamUsedP.toFixed(2)<=33) {
      $("#ServerStatsRamUsedP").attr("class", "progress-bar progress-bar-success");
    }
    else if (RamUsedP.toFixed(2)>=60) {
      $("#ServerStatsRamUsedP").attr("class", "progress-bar progress-bar-danger");
    }
    else {
      $("#ServerStatsRamUsedP").attr("class", "progress-bar progress-bar-warning");
    }
    
    if (serverStats.disk_p<=40) {
      $("#ServerStatsDiskP").attr("class", "progress-bar progress-bar-success");
    }
    else if (serverStats.disk_p>=80){
      $("#ServerStatsDiskP").attr("class", "progress-bar progress-bar-danger");
    }
    else{
      $("#ServerStatsDiskP").attr("class", "progress-bar progress-bar-warning");
    }
    });
}

$(document).ready(function(){
  ServerStatus();
});
