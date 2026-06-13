import React from "react";

// Đặt tên file là RecommendedCard.jsx
export function RecommendedCard({ 
    imageSrc = "https://via.placeholder.com/300x300", 
    description, 
    saveAmount
}) {
     return (
        <div className="group min-w-[300px] flex flex-col cursor-pointer">
            
            {/* Image Area - Khung bo tròn ở trên */}
            <div className="w-full h-64 flex justify-center items-center bg-gray-50 rounded-lg mb-4 
                            group-hover:bg-gray-100 transition-colors overflow-hidden">
                <img 
                    src={imageSrc} 
                    alt={description} 
                    className="max-w-[85%] max-h-[85%] object-contain hover:scale-105 transition-transform duration-200" 
                />
            </div>
            
            {/* Content Area - Text đơn giản không có khung */}
            <div className="flex flex-col flex-grow">
                {/* Product Description */}
                <h3 className="text-lg font-bold text-black leading-tight mb-4 flex-grow">
                    {description}
                </h3>
                
                {/* Price Area */}
                <div className="mt-auto">
                    <div className="flex items-center gap-2 mb-1">
                        <span className="text-base font-semibold text-red-600">
                            Save ${saveAmount}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    );
}