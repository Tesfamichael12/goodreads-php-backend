import { useState } from 'react';

const useLogin = () => {
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState(null);
  const [user, setUser] = useState(null);

  const login = async (username, password) => {
    setIsLoading(true);
    setError(null);
    setUser(null);
    
    try {
      const response = await fetch('../backend/auth/login.php', {
        method: 'POST',
        credentials: 'include', // Important for sessions to work
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ username, password }),
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
      setIsLoading(false);
    }
  };

  return { login, isLoading, error, user };
};

export default useLogin;