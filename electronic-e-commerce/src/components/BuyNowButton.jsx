

import React from 'react';

const BuyNowButton = ({ text = "Buy now" }) => {
  return (
    <button
      className="
        bg-black               /* Màu nền: Đen */
        text-white           /* Màu chữ: Trắng */
        text-sm                /* Kích thước chữ */
        font-semibold          /* Độ đậm của chữ */
        px-4                 /* Padding ngang */
        py-2                 /* Padding dọc */
        rounded-full           /* Bo tròn hoàn toàn (Rất quan trọng) */
        shadow-lg              /* Đổ bóng nhẹ */
        hover:opacity-80       /* Hiệu ứng khi di chuột */
        transition-opacity     /* Chuyển đổi mượt mà */
      "
    >
      {text}
    </button>
  );
};

export default BuyNowButton;