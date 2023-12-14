import React, { useState } from 'react';
import axios from 'axios';

const Register = () => {
  const [formData, setFormData] = useState({
    name: '',
    username: '',
    password: '',
    password_confirmation: '',
  });
  const [message, setMessage] = useState('');

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const response = await axios.post('http://127.0.0.1:8000/api/register', formData);

      const data = response.data;
      console.log(data);

      if (data.status === 201) {
        setMessage(`User ${formData.name} is created successfully. Token: ${data.data.token}`);

        setFormData({
          name: '',
          username: '',
          password: '',
          password_confirmation: '',
        });
      } else {
        setMessage('Failed to register. Please check your information.');
      }
    } catch (error) {
      console.error(error);
      setMessage('An error occurred. Please try again later.');
    }
  };

  return (
    <div className="max-w-md mx-auto mt-8 p-4 bg-white shadow-md rounded-md">
      <h2 className="text-2xl font-bold mb-4">Register</h2>
      <form onSubmit={handleSubmit}>
        <input
          className="w-full p-2 mb-2 rounded-md border border-gray-300 focus:outline-none focus:border-blue-500"
          type="text"
          name="name"
          placeholder="Name"
          value={formData.name}
          onChange={handleChange}
        />
        <input
          className="w-full p-2 mb-2 rounded-md border border-gray-300 focus:outline-none focus:border-blue-500"
          type="text"
          name="username"
          placeholder="Username"
          value={formData.username}
          onChange={handleChange}
        />
        <input
          className="w-full p-2 mb-2 rounded-md border border-gray-300 focus:outline-none focus:border-blue-500"
          type="password"
          name="password"
          placeholder="Password"
          value={formData.password}
          onChange={handleChange}
        />
        <input
          className="w-full p-2 mb-2 rounded-md border border-gray-300 focus:outline-none focus:border-blue-500"
          type="password"
          name="password_confirmation"
          placeholder="Confirm Password"
          value={formData.password_confirmation}
          onChange={handleChange}
        />
        <button className="w-full bg-blue-500 text-white rounded-md py-2" type="submit">
          Register
        </button>
      </form>
      {message && <p className="mt-4 text-red-500">{message}</p>}
    </div>
  );
};

export default Register;
