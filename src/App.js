import React from "react";
import Navbar from "./Components/Navbar";
import HomePage from "./Components/HomePage";
import Footer from "./Components/Footer";
import "bootstrap/dist/css/bootstrap.min.css"; // Ensure Bootstrap is imported

function App() {
  return (
    <div>
      <div className="d-flex flex-column min-vh-100">
        <div className="flex-grow-1">
          <Navbar />
          <HomePage />
        </div>
        <Footer />
      </div>
    </div>
  );
}

export default App;
