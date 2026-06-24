import React from 'react';
import { User, ShoppingCart, LogOut } from 'lucide-react';


const accountMenuItems = [
  { name: "My Orders", icon: ShoppingCart },
  { name: "My Page & Products" },
  { name: "My Rewards" },
  { name: "Product Registration" },
  { name: "Samsung Account" },
];






function UserAccountPopup({ isVisible }) {
  if (!isVisible) return null;

  return (



    <div
      className="absolute top-12 right-0 w-64 bg-white shadow-2xl rounded-xl p-4 transform origin-top-right transition-all duration-300 ease-out"
      style={{

        zIndex: 50,

        opacity: isVisible ? 1 : 0,
        transform: isVisible ? 'scale(1)' : 'scale(0.95)',
      }}
    >

      { }
      <div className="flex items-center space-x-3 border-b pb-4 mb-4">
        <div className="p-2 bg-gray-100 rounded-full">
          { }
          <User className="w-5 h-5 text-gray-700" />
        </div>
        <p className="font-semibold text-lg text-gray-900">
          Phan Phuc
        </p>
      </div>

      {/* 2. Danh sách Menu */}
      <ul className="space-y-3 text-gray-700">
        {accountMenuItems.map((item, index) => (
          <li key={index}>
            <a
              href="#"
              className="text-sm block py-1 hover:text-blue-600 transition"
            >
              {item.name}
            </a>
          </li>
        ))}
      </ul>

      { }
      <div className="border-t pt-4 mt-4">
        <a
          href="#"
          className="flex items-center text-sm font-semibold text-gray-700 hover:text-red-500 transition"
        >
          <LogOut className="w-4 h-4 mr-2" />
          Log Out
        </a>
      </div>
    </div>
  );
}

export default UserAccountPopup;