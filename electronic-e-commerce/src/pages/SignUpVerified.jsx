import React from 'react';

const SignUp = () => {
  return (
    <div className="min-h-screen w-screen">
      { }
     <nav className="p-4 bg-white border-b border-gray-100">
        <div className="max-w-7xl mx-auto">
          <ul className="flex justify-between items-center">
            { }
            <li className="text-xl font-semibold text-gray-800">
              Samsung Account
            </li>
            {/* Placeholder for the top-right icon (e.g., help/settings) */}
            <li className="text-gray-500 hover:text-gray-800 cursor-pointer">
              {/* This would typically be an icon, but we'll use a placeholder for simplicity */}
              <div className="w-6 h-6 border rounded-full bg-white flex items-center justify-center text-xs">
                ?
              </div>
            </li>
          </ul>
        </div>
      </nav>
    </div>
  );
};

export default SignUp;