import { useState } from "react";
import { Link } from "react-router-dom";
import { ChevronRight, Phone, Mail, MapPin, Clock } from "lucide-react";
import Navbar from "../components/Navbar";
import Footer from "../components/Footer";

function ContactUs() {
  const [formData, setFormData] = useState({
    name: "",
    email: "",
    phone: "",
    subject: "",
    message: ""
  });

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    alert("Thank you for your message! We will get back to you soon.");
    setFormData({ name: "", email: "", phone: "", subject: "", message: "" });
  };

  return (
    <div className="flex flex-col min-h-screen bg-white w-screen">
      <Navbar isTransparent={false} />

      { }
      <section className="mt-16 min-h-[220px] flex items-center justify-center" style={{ backgroundColor: '#e8f4f8' }}>
        <div className="text-center px-4 py-12">
          <p className="text-gray-600 text-sm mb-3 font-medium">We're here to help</p>
          <h1 className="text-4xl md:text-5xl font-bold text-black">Contact Us</h1>
        </div>
      </section>

      {/* How can we help you Section */}
      <section className="py-16 px-4 md:px-16 lg:px-24 bg-white">
        <div className="w-full">
          <h2 className="text-2xl md:text-3xl font-bold text-center mb-12">How can we help you?</h2>

          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-5">
            {/* Account */}
            <div className="bg-white border border-gray-200 rounded-2xl p-8 text-center hover:shadow-lg transition-shadow cursor-pointer min-h-[220px] flex flex-col items-center justify-center">
              <div className="flex items-center justify-center mb-6">
                <svg className="w-16 h-16" viewBox="0 0 48 48" fill="none" stroke="currentColor" strokeWidth="1.2">
                  <circle cx="24" cy="16" r="8"/>
                  <path d="M8 42c0-8 8-12 16-12s16 4 16 12"/>
                </svg>
              </div>
              <p className="text-sm font-semibold uppercase tracking-wide">Account Help</p>
            </div>

            {/* Order Help */}
            <div className="bg-white border border-gray-200 rounded-2xl p-8 text-center hover:shadow-lg transition-shadow cursor-pointer min-h-[220px] flex flex-col items-center justify-center">
              <div className="flex items-center justify-center mb-6">
                <svg className="w-16 h-16" viewBox="0 0 48 48" fill="none" stroke="currentColor" strokeWidth="1.2">
                  <path d="M6 16l18 10 18-10"/>
                  <path d="M6 16v16l18 10 18-10V16"/>
                  <path d="M24 26v16"/>
                </svg>
              </div>
              <p className="text-sm font-semibold uppercase tracking-wide">Order Help</p>
            </div>

            {/* Shipping */}
            <div className="bg-white border border-gray-200 rounded-2xl p-8 text-center hover:shadow-lg transition-shadow cursor-pointer min-h-[220px] flex flex-col items-center justify-center">
              <div className="flex items-center justify-center mb-6">
                <svg className="w-16 h-16" viewBox="0 0 48 48" fill="none" stroke="currentColor" strokeWidth="1.2">
                  <rect x="4" y="16" width="28" height="20" rx="2"/>
                  <path d="M32 22h8l4 8v6h-12"/>
                  <circle cx="12" cy="38" r="4"/>
                  <circle cx="36" cy="38" r="4"/>
                </svg>
              </div>
              <p className="text-sm font-semibold uppercase tracking-wide">Shipping Info</p>
            </div>

            {/* Returns */}
            <div className="bg-white border border-gray-200 rounded-2xl p-8 text-center hover:shadow-lg transition-shadow cursor-pointer min-h-[220px] flex flex-col items-center justify-center">
              <div className="flex items-center justify-center mb-6">
                <svg className="w-16 h-16" viewBox="0 0 48 48" fill="none" stroke="currentColor" strokeWidth="1.2">
                  <path d="M4 24h28M4 24l8-8M4 24l8 8"/>
                  <path d="M44 24H16M44 24l-8-8M44 24l-8 8"/>
                </svg>
              </div>
              <p className="text-sm font-semibold uppercase tracking-wide">Returns</p>
            </div>

            {/* Payment */}
            <div className="bg-white border border-gray-200 rounded-2xl p-8 text-center hover:shadow-lg transition-shadow cursor-pointer min-h-[220px] flex flex-col items-center justify-center">
              <div className="flex items-center justify-center mb-6">
                <svg className="w-16 h-16" viewBox="0 0 48 48" fill="none" stroke="currentColor" strokeWidth="1.2">
                  <rect x="4" y="10" width="40" height="28" rx="4"/>
                  <path d="M4 20h40"/>
                  <path d="M12 30h8"/>
                </svg>
              </div>
              <p className="text-sm font-semibold uppercase tracking-wide">Payment</p>
            </div>

            {/* Warranty */}
            <div className="bg-white border border-gray-200 rounded-2xl p-8 text-center hover:shadow-lg transition-shadow cursor-pointer min-h-[220px] flex flex-col items-center justify-center">
              <div className="flex items-center justify-center mb-6">
                <svg className="w-16 h-16" viewBox="0 0 48 48" fill="none" stroke="currentColor" strokeWidth="1.2">
                  <path d="M24 4L6 12v12c0 11 8 18 18 22 10-4 18-11 18-22V12L24 4z"/>
                  <path d="M16 24l6 6 10-12"/>
                </svg>
              </div>
              <p className="text-sm font-semibold uppercase tracking-wide">Warranty</p>
            </div>
          </div>
        </div>
      </section>

      {/* Contact Form & Info Section */}
      <section className="py-16 px-4 md:px-8" style={{ backgroundColor: '#f8f9fa' }}>
        <div className="max-w-6xl mx-auto">
          <h2 className="text-2xl md:text-3xl font-bold text-center mb-4">Get in Touch</h2>
          <p className="text-gray-600 text-center mb-12">Have a question? We'd love to hear from you.</p>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">
            {/* Contact Form */}
            <div className="bg-white rounded-2xl p-8 shadow-sm">
              <h3 className="text-xl font-bold mb-6">Send us a message</h3>
              <form onSubmit={handleSubmit} className="space-y-5">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                    <input
                      type="text"
                      name="name"
                      value={formData.name}
                      onChange={handleChange}
                      required
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                      placeholder="Your name"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                    <input
                      type="email"
                      name="email"
                      value={formData.email}
                      onChange={handleChange}
                      required
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                      placeholder="your@email.com"
                    />
                  </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input
                      type="tel"
                      name="phone"
                      value={formData.phone}
                      onChange={handleChange}
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                      placeholder="Your phone number"
                    />
                  </div>
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                    <select
                      name="subject"
                      value={formData.subject}
                      onChange={handleChange}
                      required
                      className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                    >
                      <option value="">Select a topic</option>
                      <option value="order">Order Inquiry</option>
                      <option value="product">Product Question</option>
                      <option value="shipping">Shipping & Delivery</option>
                      <option value="return">Returns & Refunds</option>
                      <option value="technical">Technical Support</option>
                      <option value="other">Other</option>
                    </select>
                  </div>
                </div>

                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                  <textarea
                    name="message"
                    value={formData.message}
                    onChange={handleChange}
                    required
                    rows="5"
                    className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition resize-none"
                    placeholder="How can we help you?"
                  ></textarea>
                </div>

                <button
                  type="submit"
                  className="w-full py-4 rounded-full text-white font-medium transition-opacity hover:opacity-90"
                  style={{ backgroundColor: '#1428a0' }}
                >
                  Send Message
                </button>
              </form>
            </div>

            { }
            <div className="space-y-6">
              <div className="bg-white rounded-2xl p-8 shadow-sm">
                <h3 className="text-xl font-bold mb-6">Contact Information</h3>
                <div className="space-y-5">
                  <div className="flex items-start gap-4">
                    <div className="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                      <Phone className="w-5 h-5 text-gray-800" />
                    </div>
                    <div>
                      <p className="font-semibold text-gray-900">Phone</p>
                      <p className="text-gray-600">1900-1234-567</p>
                      <p className="text-sm text-gray-500">Mon - Sat: 8AM - 9PM</p>
                    </div>
                  </div>

                  <div className="flex items-start gap-4">
                    <div className="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                      <Mail className="w-5 h-5 text-gray-800" />
                    </div>
                    <div>
                      <p className="font-semibold text-gray-900">Email</p>
                      <p className="text-gray-600">support@techstore.com</p>
                      <p className="text-sm text-gray-500">We reply within 24 hours</p>
                    </div>
                  </div>

                  <div className="flex items-start gap-4">
                    <div className="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                      <MapPin className="w-5 h-5 text-gray-800" />
                    </div>
                    <div>
                      <p className="font-semibold text-gray-900">Address</p>
                      <p className="text-gray-600">123 Tech Street, District 1</p>
                      <p className="text-sm text-gray-500">Ho Chi Minh City, Vietnam</p>
                    </div>
                  </div>

                  <div className="flex items-start gap-4">
                    <div className="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                      <Clock className="w-5 h-5 text-gray-800" />
                    </div>
                    <div>
                      <p className="font-semibold text-gray-900">Working Hours</p>
                      <p className="text-gray-600">Mon - Sat: 8:00 AM - 9:00 PM</p>
                      <p className="text-sm text-gray-500">Sunday: 9:00 AM - 6:00 PM</p>
                    </div>
                  </div>
                </div>
              </div>

              { }
              <div className="bg-white rounded-2xl p-8 shadow-sm">
                <h3 className="text-xl font-bold mb-4">Quick Links</h3>
                <div className="space-y-3">
                  <Link to="#" className="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition group">
                    <span className="font-medium text-gray-700">Track Your Order</span>
                    <ChevronRight className="w-5 h-5 text-gray-400 group-hover:text-blue-600" />
                  </Link>
                  <Link to="#" className="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition group">
                    <span className="font-medium text-gray-700">Return Policy</span>
                    <ChevronRight className="w-5 h-5 text-gray-400 group-hover:text-blue-600" />
                  </Link>
                  <Link to="#" className="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition group">
                    <span className="font-medium text-gray-700">FAQs</span>
                    <ChevronRight className="w-5 h-5 text-gray-400 group-hover:text-blue-600" />
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Business Support Banner */}
      <section className="py-12 px-4 md:px-8">
        <div className="max-w-4xl mx-auto">
          <div className="bg-gradient-to-r from-gray-900 to-gray-800 rounded-2xl p-8 md:p-12 text-center">
            <h2 className="text-2xl md:text-3xl font-bold text-white mb-4">
              Need Business Support?
            </h2>
            <p className="text-gray-300 mb-6">
              Get dedicated support for bulk orders and business solutions.
            </p>
            <button
              className="px-8 py-3 rounded-full text-sm font-medium transition-opacity hover:opacity-90"
              style={{ backgroundColor: 'white', color: 'black' }}
            >
              Contact Business Support
            </button>
          </div>
        </div>
      </section>

      <Footer />
    </div>
  );
}

export default ContactUs;
