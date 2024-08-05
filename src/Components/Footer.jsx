import React from "react";
import "bootstrap/dist/css/bootstrap.min.css"; // Ensure Bootstrap is imported
import "./Footer.css";

const Footer = () => {
  return (
    <footer className="footer bg-light py-3">
      <div className="container text-center">
        <span className="text-muted">
          {/* &copy; 2024 Board of Technical Examinations, Karnataka. All rights
          reserved. */}
          <strong>NOTE : </strong> This is not an official website. It was made
          by a diploma student,{" "}
          <a
            href="https://www.instagram.com/rahemansk._/?igsh=MW8wYnp3MG01dnNtZA%3D%3D"
            id="contact-student"
          >
            <strong>Raheman</strong>
          </a>
        </span>
      </div>
    </footer>
  );
};

export default Footer;
