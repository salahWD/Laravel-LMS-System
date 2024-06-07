if (playerNeeded) {
  // prevent space scroll down
  window.addEventListener("keydown", function (e) {
    if (e.keyCode == 32 && e.target == document.body) {
      e.preventDefault();
    }
  });

  window.onload = function () {
    let video = document.getElementById("video");
    let videoHolder = document.getElementById("video-holder");
    let startBtn = document.getElementById("start-stop");
    let volume = document.getElementById("volume");
    let muteControl = document.getElementById("mute-control");
    let volumeInput = document.getElementById("volume-level");
    let activeArea = document.getElementById("input-area");
    let inputThumn = document.getElementById("input-thumb");
    let line = document.getElementById("line");
    let hoveredLine = document.getElementById("hovered-line");
    let loadedLine = document.getElementById("loaded-line");
    let watchedLine = document.getElementById("watched-line");
    let watchedThumb = document.getElementById("watched-thumb");
    let screenBtn = document.getElementById("screen-btn");
    let timestamp = document.getElementById("time-stamp");
    let currentTime = document.getElementById("current-time");
    let isChanging = false;
    let fullscreen = false;

    video.focus();
    // $("#next-lecture").fadeOut(5);
    timestamp.innerHTML = Math.round(video.duration);
    setWatched(0);
    changeVolume(60);

    function startVideo() {
      startBtn.classList.toggle("active");
      if (startBtn.classList.contains("active")) {
        video.play();
        videoHolder.classList.remove("stop");
        document.querySelector(".video .card.stop").classList.add("active");
        setTimeout(() => {
          document
            .querySelector(".video .card.stop")
            .classList.remove("active");
        }, 500);
      } else {
        video.pause();
        videoHolder.classList.add("stop");
        document.querySelector(".video .card.start").classList.add("active");
        setTimeout(() => {
          document
            .querySelector(".video .card.start")
            .classList.remove("active");
        }, 500);
      }
    }

    function changeVolume(vol = 50) {
      video.volume = vol / 100;

      activeArea.style.width = `${Math.ceil(
        (vol / 100) * volumeInput.clientWidth
      )}px`;
      inputThumn.style.left = `${Math.ceil(
        (vol / 100) * (volumeInput.clientWidth - 8)
      )}px`;

      if (vol >= 50) {
        volume.classList.add("high");
        volume.classList.remove("low");
        volume.classList.remove("mute");
      } else if (vol < 50 && vol > 0) {
        volume.classList.remove("high");
        volume.classList.add("low");
        volume.classList.remove("mute");
      } else if (vol == 0) {
        volume.classList.remove("high");
        volume.classList.remove("low");
        volume.classList.add("mute");
      } else {
        volume.classList.remove("high");
        volume.classList.remove("low");
        volume.classList.add("mute");
      }
    }

    function mute() {
      if (volume.classList.contains("mute")) {
        volume.classList.remove("mute");
        video.muted = false;
      } else {
        volume.classList.add("mute");
        video.muted = true;
      }
    }

    function toggleFullScreen() {
      if (fullscreen) {
        if (video.webkitExitFullscreen) {
          video.webkitExitFullscreen();
        } else if (video.mozCancelFullscreen) {
          /* Safari */
          video.mozCancelFullscreen();
        } else if (video.exitFullscreen) {
          /* IE11 */
          video.exitFullscreen();
        }
      } else {
        if (video.requestFullscreen) {
          video.requestFullscreen();
        } else if (video.webkitRequestFullscreen) {
          /* Safari */
          video.webkitRequestFullscreen();
        } else if (video.msRequestFullscreen) {
          /* IE11 */
          video.msRequestFullscreen();
        }
      }
    }

    function setBuffer(buffer) {
      const percentage = buffer / video.duration;
      loadedLine.style.width = `${percentage * line.clientWidth}px`;
      console.log(`percentage is ${percentage * 100}%`);
    }

    function updateWatched() {
      const duration = video.duration;
      if (duration > 0) {
        line.style.setProperty(
          "--watched-width",
          `${(video.currentTime / duration) * line.clientWidth}px`
        );
      }
    }

    function setWatched(percent) {
      currentTime.innerHTML = Math.round(percent * video.duration);
      video.currentTime = percent * video.duration;
      updateWatched();
    }

    video.addEventListener("contextmenu", (e) => e.preventDefault());

    video.addEventListener("click", function (e) {
      startVideo();
    });
    startBtn.addEventListener("click", function (e) {
      startVideo();
    });
    volumeInput.addEventListener("input", function (e) {
      changeVolume(this.value);
    });
    muteControl.addEventListener("click", function () {
      mute();
    });

    line.addEventListener("mousemove", function (e) {
      let { x } = line.getBoundingClientRect();
      hoveredLine.style.width = `${e.clientX - x}px`;
      if (e.buttons == 1 && e.button == 0) {
        let percentage = (e.clientX - x) / line.clientWidth;
        setWatched(percentage);
      }
    });
    line.addEventListener("mouseleave", () => (hoveredLine.style.width = "0"));
    line.addEventListener("click", function (e) {
      let { x } = line.getBoundingClientRect();
      let percentage = (e.clientX - x) / line.clientWidth;
      setWatched(percentage);
    });

    screenBtn.addEventListener("click", toggleFullScreen);

    video.addEventListener("fullscreenchange", function (e) {
      fullscreen = !fullscreen;
    });
    video.addEventListener("webkitfullscreenchange", function (e) {
      fullscreen = !fullscreen;
    });
    video.addEventListener("mozfullscreenchange", function (e) {
      fullscreen = !fullscreen;
    });

    window.addEventListener("keydown", function (e) {
      if (e.key == " " || e.key == "k" || e.key == "K" || e.key == "ن") {
        startVideo();
      } else if (e.key == "f" || e.key == "F" || e.key == "ب") {
        toggleFullScreen();
      } else if (e.key == "ArrowUp") {
        vol = Math.round(+video.volume * 100);
        if (vol < 100) {
          changeVolume(vol + 5);
        }
      } else if (e.key == "ArrowDown") {
        vol = Math.round(+video.volume * 100);
        if (vol > 5) {
          changeVolume(vol - 5);
        } else {
          changeVolume(0);
        }
      } else if (e.key == "ArrowRight") {
        setWatched((+video.currentTime + 5) / video.duration);
        document.querySelector(".video .card.forward").classList.add("active");
        setTimeout(() => {
          document
            .querySelector(".video .card.forward")
            .classList.remove("active");
        }, 500);
      } else if (e.key == "ArrowLeft") {
        setWatched((+video.currentTime - 5) / video.duration);
        document.querySelector(".video .card.backward").classList.add("active");
        setTimeout(() => {
          document
            .querySelector(".video .card.backward")
            .classList.remove("active");
        }, 500);
      } else if (e.key == "l" || e.key == "L" || e.key == "م") {
        setWatched((+video.currentTime + 10) / video.duration);
        document.querySelector(".video .card.forward").classList.add("active");
        setTimeout(() => {
          document
            .querySelector(".video .card.forward")
            .classList.remove("active");
        }, 500);
      } else if (e.key == "j" || e.key == "J" || e.key == "ت") {
        setWatched((+video.currentTime - 10) / video.duration);
        document.querySelector(".video .card.backward").classList.add("active");
        setTimeout(() => {
          document
            .querySelector(".video .card.backward")
            .classList.remove("active");
        }, 500);
      } else if (parseInt(e.key) >= 0 && parseInt(e.key) <= 9) {
        setWatched(parseInt(e.key) / 10);
        console.log(video.duration);
        console.log(parseInt(e.key) / 10);
      }
    });

    video.addEventListener("progress", () => {
      if (video.duration > 0) {
        for (let i = 0; i < video.buffered.length; i++) {
          // search for last downloaded buffere and get its end()
          if (
            video.buffered.start(video.buffered.length - 1 - i) <
            video.currentTime
          ) {
            setBuffer(video.buffered.end(video.buffered.length - 1 - i));
            break;
          }
        }
      }
    });

    let trig = true;
    video.addEventListener("timeupdate", () => {
      if ((video.currentTime / video.duration) * 100 > 90 && trig) {
        trig = false;
        markLectureAsDone();
      }
      currentTime.innerHTML = Math.round(video.currentTime);
      updateWatched();
    });
  };
}

window.markLectureAsDone = function () {
  $.ajax(doneUrl, {
    method: "POST",
    headers: {
      "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    },
    success: function (res) {
      if (res.result) {
        const target = document.getElementById("next-lecture");
        target.style = "display: block";
        setTimeout(function () {
          document.getElementById("next-lecture").classList.add("animateIn");
        }, 100);
      }
    },
  });
};
