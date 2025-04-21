
const createLoginHandler = () => {
  let isLoading = false;
  let error = null;
  let user = null;
  const eventTarget = new EventTarget();

  // Private state management
  const setLoading = (state) => {
    isLoading = state;
    dispatchEvent('loading', isLoading);
  };

  const setError = (err) => {
    error = err;
    dispatchEvent('error', error);
  };

  const setUser = (userData) => {
    user = userData;
    dispatchEvent('user', user);
  };

  const dispatchEvent = (type, detail) => {
    eventTarget.dispatchEvent(new CustomEvent(type, { detail }));
  };

  // Public methods
  const login = async (email, password) => {
    setLoading(true);
    setError(null);
    setUser(null);
    
    try {
      const response = await fetch('../backend/auth/login.php', {
        method: 'POST',
        credentials: 'include',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, password }),
      });

      const data = await response.json();

      if (!response.ok || !data.success) {
        throw new Error(data.message || 'Login failed');
      }

      setUser(data.user);
      return data;
    } catch (err) {
      setError(err.message);
      return { success: false, message: err.message };
    } finally {
      setLoading(false);
    }
  };

  // Event subscription
  const on = (event, callback) => {
    eventTarget.addEventListener(event, (e) => callback(e.detail));
  };

  return {
    login,
    on,
    get isLoading() { return isLoading; },
    get error() { return error; },
    get user() { return user; }
  };
};
