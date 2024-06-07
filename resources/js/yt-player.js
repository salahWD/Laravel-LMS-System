var player;
var startedYet = false;

let url = $("#videoPreviewHolder").data("video");
let videoId = url.split("/").pop();

window.onYouTubeIframeAPIReady = function () {
  startedYet = true;
  player = new YT.Player("videoPlayer", {
    videoId: videoId,
    playerVars: {
      playersinline: 1,
      autoplay: 1,
      controls: 1,
    },
    events: {
      onReady: onPlayerReady,
      onStateChange: onPlayerStateChange,
    },
  });
};

function onPlayerReady(event) {
  event.target.playVideo();
}

function onPlayerStateChange(event) {
  if (event.data == YT.PlayerState.ENDED) {
    markLectureAsDone();
  }
}

document.addEventListener("DOMContentLoaded", function (event) {
  console.log("dom ready!");
  var tag = document.createElement("script");
  tag.src = "https://www.youtube.com/player_api";
  var firstScriptTag = document.getElementsByTagName("script")[0];
  firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
  setTimeout(function () {
    if (!startedYet) {
      onYouTubeIframeAPIReady();
    }
  }, 5000);
});
