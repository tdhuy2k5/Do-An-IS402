import api from "./api";


export const getCart = async () => {
  try {
    const response = await api.get("/auth/cart");
    return response.data;
  } catch (error) {

    if (error.response?.status === 404) {
      return { success: false, data: [], total_price: 0 };
    }

    console.error("getCart error:", error);
    return { success: false, data: [], total_price: 0 };
  }
};


export const addToCart = async (productId, variantId, quantity = 1) => {
  const response = await api.post("/auth/cart/additem", {
    product_id: productId,
    variant_id: variantId,
    quantity: quantity,
  });
  return response.data;
};


export const removeFromCart = async (cartItemId) => {
  const response = await api.delete(`/auth/cart/cart-item/${cartItemId}`);
  return response.data;
};


export const updateCartQuantity = async (cartItemId, quantity) => {
  const response = await api.patch("/auth/cart/updatequantity", {
    cart_item_id: cartItemId,
    quantity: quantity,
  });
  return response.data;
};


export const clearCart = async () => {
  const response = await api.get("/auth/cart/clear");
  return response.data;
};
