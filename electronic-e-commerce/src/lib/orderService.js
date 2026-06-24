import api from "./api";


export const createOrder = async (orderData) => {
  const response = await api.post("/auth/create-order", orderData);
  return response.data;
};


export const getAllOrders = async () => {
  const response = await api.get("/auth/getallorder");
  return response.data;
};


export const getOrderDetail = async (orderId) => {
  const response = await api.get(`/auth/order/${orderId}`);
  return response.data;
};


export const cancelOrder = async (orderId) => {
  const response = await api.get(`/auth/order/cancel/${orderId}`);
  return response.data;
};


export const checkPromoCode = async (code) => {
  const response = await api.get(`/auth/check-coupon?code=${code}`);
  return response.data;
};
