import { useEffect, useRef } from "react";

const YOUTUBE_VIDEO_ID = "ePdbj2bZ-Ro";
const LOOP_AT_SECONDS = 60;

const HeroSection = () => {
  const playerRef = useRef(null);
  const intervalRef = useRef(null);
  const timeoutRef = useRef(null);

  useEffect(() => {
    let cancelled = false;

    const initPlayer = () => {
      if (cancelled || playerRef.current) return;

      playerRef.current = new window.YT.Player("hero-youtube-player", {
        videoId: YOUTUBE_VIDEO_ID,
        playerVars: {
          autoplay: 1,
          mute: 1,
          controls: 0,
          modestbranding: 1,
          rel: 0,
          playsinline: 1,
          enablejsapi: 1,
          origin: window.location.origin,
        },
        events: {
          onReady: (event) => {
            const player = event.target;

            player.mute();

            // force autoplay (reduce play icon flicker)
            player.playVideo();
            setTimeout(() => player.playVideo(), 200);

            // loop logic (clean + stable)
            intervalRef.current = setInterval(() => {
              const time = player.getCurrentTime();
              if (time >= LOOP_AT_SECONDS) {
                player.seekTo(0, true);
              }
            }, 500);
          },
        },
      });
    };

    const loadYouTubeAPI = () => {
      if (window.YT && window.YT.Player) {
        initPlayer();
        return;
      }

      if (!document.getElementById("youtube-iframe-api")) {
        const tag = document.createElement("script");
        tag.id = "youtube-iframe-api";
        tag.src = "https://www.youtube.com/iframe_api";
        document.body.appendChild(tag);
      }

      window.onYouTubeIframeAPIReady = initPlayer;
    };

    // delay load to avoid layout flicker
    timeoutRef.current = setTimeout(loadYouTubeAPI, 300);

    return () => {
      cancelled = true;

      clearTimeout(timeoutRef.current);
      clearInterval(intervalRef.current);

      if (playerRef.current) {
        playerRef.current.destroy();
        playerRef.current = null;
      }
    };
  }, []);

  return (
    <div className="relative w-screen h-screen overflow-hidden bg-black">
      {/* YouTube Player */}
      <div className="absolute inset-0">
        <div
          id="hero-youtube-player"
          className="absolute top-1/2 left-1/2 w-[150vw] h-[150vh] -translate-x-1/2 -translate-y-1/2"
        />
      </div>

      {/* 🔥 Overlay to hide loading flash */}
      <div className="absolute inset-0 bg-black z-10 pointer-events-none opacity-0" />

      {/* Overlay Content */}
      <div className="relative z-20 flex flex-col items-center justify-center h-full text-white text-center">
        <div className="mb-40 space-y-4">
          <h1 className="text-6xl md:text-8xl font-black tracking-tighter">
            Galaxy Event
          </h1>
          <p className="text-xl md:text-2xl">
            Oct. 21, 2025 at 10:00 PM EDT
          </p>
          <p className="text-xl md:text-2xl pb-4">
            Live on Samsung.com
          </p>
          <button className="mt-8 px-8 py-3 bg-white text-black font-semibold rounded-full hover:bg-gray-200 transition">
            Reserve now
          </button>
        </div>
      </div>

      {/* Footer */}
      <div className="absolute bottom-5 left-5 text-white/50 z-30">
        <span className="text-2xl">&copy;</span>
      </div>

      {/* Feedback */}
      <div className="absolute top-1/2 right-0 -translate-y-1/2 -rotate-90 origin-bottom-right p-2 bg-gray-700/80 text-white text-xs font-medium z-30">
        FEEDBACK
      </div>
    </div>
  );
};

export default HeroSection;