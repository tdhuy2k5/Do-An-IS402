import { useState, useEffect } from "react";
import { useParams, useNavigate, Link } from "react-router-dom";
import { Loader2, Package, ArrowLeft, MapPin, Phone, User, Clock, CheckCircle, XCircle, Truck, CreditCard } from "lucide-react";
import Navbar from "../components/Navbar";
import Footer from "../components/Footer";
import api from "../lib/api";


const StatusBadge = ({ status, type = "order" }) => {
  const getStatusStyle = () => {
    if (type === "payment") {
      switch (status?.toLowerCase()) {
        case "paid":
          return { bg: "#dcfce7", color: "#166534", icon: CheckCircle, label: "Paid" };
        case "pending":
          return { bg: "#fef3c7", color: "#92400e", icon: Clock, label: "Pending Payment" };
        default:
          return { bg: "#f3f4f6", color: "#374151", icon: Clock, label: status };
      }
    }
    switch (status?.toLowerCase()) {
      case "completed":
      case "delivered":
        return { bg: "#dcfce7", color: "#166534", icon: CheckCircle, label: "Delivered" };
      case "shipping":
      case "shipped":
        return { bg: "#dbeafe", color: "#1e40af", icon: Truck, label: "Shipping" };
      case "pending":
      case "processing":
        return { bg: "#fef3c7", color: "#92400e", icon: Clock, label: "Processing" };
      case "cancelled":
      case "canceled":
        return { bg: "#fee2e2", color: "#991b1b", icon: XCircle, label: "Cancelled" };
      default:
        return { bg: "#f3f4f6", color: "#374151", icon: Clock, label: status };
    }
  };

  const style = getStatusStyle();
  const Icon = style.icon;

  return (
    <span
      className="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium"
      style={{ backgroundColor: style.bg, color: style.color }}
    >
      <Icon style={{ width: "16px", height: "16px" }} />
      {style.label?.toUpperCase() || "UNKNOWN"}
    </span>
  );
};

const OrderDetail = () => {
  const { orderId } = useParams();
  const navigate = useNavigate();
  const [order, setOrder] = useState(null);
  const [orderItems, setOrderItems] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [isCancelling, setIsCancelling] = useState(false);

  useEffect(() => {
    const fetchOrderDetail = async () => {
      const accessToken = localStorage.getItem("access_token");
      if (!accessToken) {
        navigate("/login");
        return;
      }

      try {
        const response = await api.get(`/auth/order/${orderId}`);
        if (response.data.order) {
          setOrder(response.data.order);
          setOrderItems(response.data.items || []);
        }
      } catch (error) {
        console.error("Error fetching order:", error);
        if (error.response?.status === 404) {
          navigate("/my-orders");
        }
      } finally {
        setIsLoading(false);
      }
    };

    if (orderId) {
      fetchOrderDetail();
    }
  }, [orderId, navigate]);

  const handleCancelOrder = async () => {
    if (!window.confirm("Are you sure you want to cancel this order?")) return;

    setIsCancelling(true);
    try {
      await api.get(`/auth/order/cancel/${orderId}`);
      alert("Order cancelled successfully");
      navigate("/my-orders");
    } catch (error) {
      alert(error.response?.data?.message || "Failed to cancel order");
    } finally {
      setIsCancelling(false);
    }
  };

  const canCancel = order?.status?.toLowerCase() === "pending" && order?.payment_status?.toLowerCase() === "pending";

  const createdAt = order?.created_at ? new Date(order.created_at).toLocaleDateString("en-US", {
    year: "numeric",
    month: "long",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  }) : "N/A";

  if (isLoading) {
    return (
      <div className="flex flex-col min-h-screen w-screen bg-gray-50">
        <Navbar isTransparent={false} />
        <div className="flex-grow flex items-center justify-center">
          <Loader2 className="w-8 h-8 animate-spin text-blue-600" />
        </div>
        <Footer />
      </div>
    );
  }

  if (!order) {
    return (
      <div className="flex flex-col min-h-screen w-screen bg-gray-50">
        <Navbar isTransparent={false} />
        <div className="flex-grow flex items-center justify-center">
          <div className="text-center">
            <Package className="w-16 h-16 text-gray-300 mx-auto mb-4" />
            <h2 className="text-xl font-bold mb-2">Order not found</h2>
            <Link to="/my-orders" className="text-blue-600 hover:underline">
              Back to My Orders
            </Link>
          </div>
        </div>
        <Footer />
      </div>
    );
  }

  return (
    <div className="flex flex-col min-h-screen w-screen bg-gray-50">
      <Navbar isTransparent={false} />

      <main className="flex-grow w-full mt-16 pb-20">
        {/* Header */}
        <div className="w-full bg-white border-b border-gray-200">
          <div className="max-w-5xl mx-auto px-4 py-6">
            <Link to="/my-orders" className="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-4">
              <ArrowLeft style={{ width: "20px", height: "20px" }} />
              Back to My Orders
            </Link>
            <div className="flex items-start justify-between">
              <div>
                <h1 className="text-2xl font-bold">Order #{order.order_id}</h1>
                <p className="text-sm text-gray-500 mt-1">Placed on {createdAt}</p>
              </div>
              <div className="flex flex-col items-end gap-2">
                <StatusBadge status={order.status} type="order" />
                <StatusBadge status={order.payment_status} type="payment" />
              </div>
            </div>
          </div>
        </div>

        <div className="max-w-5xl mx-auto px-4 py-6">
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {/* Left - Order Items */}
            <div className="lg:col-span-2 space-y-6">
              {/* Items */}
              <div className="bg-white rounded-lg border border-gray-200 p-6">
                <h2 className="font-bold text-lg mb-4 flex items-center gap-2">
                  <Package style={{ width: "20px", height: "20px" }} />
                  Order Items ({orderItems.length})
                </h2>

                <div className="space-y-4">
                  {orderItems.map((item, index) => (
                    <div key={index} className="flex gap-4 pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                      <div className="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <Package className="w-8 h-8 text-gray-400" />
                      </div>
                      <div className="flex-1">
                        <p className="font-medium">{item.product_name}</p>
                        <p className="text-sm text-gray-500">Quantity: {item.quantity}</p>
                        <p className="text-sm text-gray-500">Unit Price: ${parseFloat(item.unit_price).toFixed(2)}</p>
                      </div>
                      <div className="text-right">
                        <p className="font-bold">${parseFloat(item.total_price).toFixed(2)}</p>
                      </div>
                    </div>
                  ))}
                </div>
              </div>

              {/* Order Notes */}
              {order.customer_note && (
                <div className="bg-white rounded-lg border border-gray-200 p-6">
                  <h2 className="font-bold text-lg mb-2">Order Notes</h2>
                  <p className="text-gray-600">{order.customer_note}</p>
                </div>
              )}
            </div>

            {/* Right - Summary */}
            <div className="space-y-6">
              {/* Price Summary */}
              <div className="bg-white rounded-lg border border-gray-200 p-6">
                <h2 className="font-bold text-lg mb-4">Order Summary</h2>

                <div className="space-y-2">
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">Subtotal</span>
                    <span>${parseFloat(order.subtotal || 0).toFixed(2)}</span>
                  </div>
                  {order.discount_amount > 0 && (
                    <div className="flex justify-between text-sm">
                      <span className="text-gray-600">Discount</span>
                      <span style={{ color: "#10b981" }}>-${parseFloat(order.discount_amount).toFixed(2)}</span>
                    </div>
                  )}
                  <div className="flex justify-between text-sm">
                    <span className="text-gray-600">Shipping</span>
                    <span>${parseFloat(order.shipping_fee || 10).toFixed(2)}</span>
                  </div>
                  <div className="flex justify-between font-bold text-lg pt-3 mt-3 border-t border-gray-200">
                    <span>Total</span>
                    <span>${parseFloat(order.total_amount || 0).toFixed(2)}</span>
                  </div>
                </div>
              </div>

              {/* Actions */}
              {canCancel && (
                <button
                  onClick={handleCancelOrder}
                  disabled={isCancelling}
                  className="w-full py-3 rounded-lg font-medium text-red-600 border border-red-200 hover:bg-red-50 transition disabled:opacity-50 flex items-center justify-center gap-2"
                >
                  {isCancelling ? (
                    <>
                      <Loader2 className="w-4 h-4 animate-spin" />
                      Cancelling...
                    </>
                  ) : (
                    <>
                      <XCircle style={{ width: "18px", height: "18px" }} />
                      Cancel Order
                    </>
                  )}
                </button>
              )}

              {/* Help */}
              <div className="bg-blue-50 rounded-lg p-4">
                <p className="text-sm font-medium mb-1">Need Help?</p>
                <p className="text-xs text-gray-600 mb-2">Contact our support team for any questions about your order.</p>
                <Link to="/contact-us" className="text-sm text-blue-600 hover:underline">
                  Contact Support →
                </Link>
              </div>
            </div>
          </div>
        </div>
      </main>

      <Footer />
    </div>
  );
};

export default OrderDetail;
