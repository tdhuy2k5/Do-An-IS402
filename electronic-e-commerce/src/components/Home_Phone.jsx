
import React from "react";

export default function Home_Phone({
  bgImage,
  title = "Galaxy Z Fold7",
  subTitle = "Galaxy AI ✨"
}) {
  return (
    <div className="relative h-[90vh] max-w-7xl mx-auto overflow-hidden flex items-center justify-center mt-20 px-4 md:px-8">

      { }
      <div className="absolute inset-0 z-0 rounded-1xl overflow-hidden bg-black"> { }
        <img
          src={bgImage}
          alt={title}
          className="w-full h-full object-cover"

          onError={(e) => {
            console.error("Link ảnh bị lỗi hoặc không truy cập được:", bgImage);
            e.target.style.display = 'none';
          }}
        />
        { }
        <div className="absolute inset-0 bg-opacity-30" />
      </div>

      {/* NỘI DUNG CHỮ */}
      <div className="relative z-10 flex h-full flex-col items-center justify-center text-white text-center px-4 pointer-events-none">
        <h1 className="text-5xl md:text-7xl font-extrabold leading-tight drop-shadow-lg">
          {title}
        </h1>
        <p className="mt-2 text-xl md:text-2xl font-light mb-8 drop-shadow-md">
          {subTitle}
        </p>
      </div>
    </div>
  );
}