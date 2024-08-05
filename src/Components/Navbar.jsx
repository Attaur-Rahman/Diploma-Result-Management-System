import React, { useEffect, useState } from "react";
import GovtLogo from "../images/Govt-Logo.png";
import "./Navbar.css";

const Navbar = () => {
  // State to hold the updates to be displayed in the marquee
  const [updates, setUpdates] = useState([]);

  // Function to simulate fetching updates from an API
  const fetchUpdates = () => {
    const updatesData = [
      "Results of Nov/Dec - 2021",
      "Apr/May - 2022",
      "Aug/Sep - 2022",
      "Nov/Dec - 2022",
      "Jan/Feb - 2023",
      "Jun/Jul - 2023",
      "Nov/Dec - 2023 Diploma Examinations",
    ];

    // Return a Promise that resolves with the updates data after a delay
    return new Promise((resolve) => {
      setTimeout(() => {
        resolve(updatesData);
      }, 5000);
    });
  };

  // Use useEffect to fetch updates when the component mounts
  useEffect(() => {
    const fetchAndUpdateUpdates = async () => {
      const fetchedUpdates = await fetchUpdates();
      setUpdates(fetchedUpdates);
    };

    fetchAndUpdateUpdates();

    // Set up an interval to fetch updates every 5 seconds
    const intervalId = setInterval(fetchAndUpdateUpdates, 5000);

    // Clean up the interval when the component unmounts
    return () => clearInterval(intervalId);
  }, []);

  return (
    <div>
      {/* Navbar */}
      <nav className="navbar navbar-expand-lg">
        <div className="container-fluid">
          <a href="/" className="navbar-brand d-flex align-items-center">
            <img src={GovtLogo} alt="kar-logo-error" width="90" />
            <span className="bte text-start d-block">
              <span className="text-wrap">
                Board of Technical Examinations Department of Technical
                Education, Karnataka <br />
              </span>
              <span className="m-y text-start d-block text-wrap">
                Diploma Examinations Result
              </span>
            </span>
          </a>
          <div className="d-grid gap-2 d-md-flex justify-content-md-end">
            <a
              href="http://website-name/bte/AdminLoginPage.php"
              className="btn btn-light"
            >
              Login
            </a>
          </div>
        </div>
      </nav>

      {/* Alert with marquee */}
      <div className="alert alert-danger" role="alert">
        {/* eslint-disable-next-line jsx-a11y/no-distracting-elements */}
        <marquee behavior="scroll" direction="left" className="marquee">
          NOTE: This is not an official website; it's a replica made by a
          diploma student,{" "}
          <a
            href="https://www.instagram.com/rahemansk._/?igsh=MW8wYnp3MG01dnNtZA%3D%3D"
            id="contact-student"
          >
            Raheman
          </a>
          . If you want your result to be displayed here, you can send him your
          marksheet.
        </marquee>
      </div>

      {/* Marquee for updates */}
      <div className="marquee-container">
        {/* eslint-disable-next-line jsx-a11y/no-distracting-elements */}
        <marquee behavior="scroll" direction="left" className="marquee">
          {updates.join(" | ")}
        </marquee>
      </div>
    </div>
  );
};

export default Navbar;
