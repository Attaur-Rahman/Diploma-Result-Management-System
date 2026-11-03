import React, { useEffect, useState } from "react";
import GovtLogo from "../images/Govt-Logo.png";
import NewGIF from "../images/new.gif";
import "./Navbar.css";

const Navbar = () => {
  const [updates, setUpdates] = useState([]);

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

    // Simulate fetching updates from an API by returning a Promise
    return new Promise((resolve) => {
      setTimeout(() => {
        resolve(updatesData); // Resolve with the updates data after 5 seconds
      });
    });
  };

  // Usage
  useEffect(() => {
    const fetchAndUpdateUpdates = async () => {
      const fetchedUpdates = await fetchUpdates();
      setUpdates(fetchedUpdates);
    };

    fetchAndUpdateUpdates();

    // Interval for fetching updates every 5 seconds
    const intervalId = setInterval(fetchAndUpdateUpdates, 5000);

    // Cleanup function to clear the interval when the component unmounts
    return () => clearInterval(intervalId);
  }, []);

  return (
    <div>
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
                Diploma Examinations Result{" "}
              </span>
            </span>
          </a>
          <div className="d-grid gap-2 d-md-flex justify-content-md-end">
            <a
              href="/bte/AdminLoginPage"
              className="btn btn-light"
            >
              Login
            </a>
          </div>
        </div>
      </nav>
      {/* Marquee for updates */}
      {/*eslint-disable-next-line jsx-a11y/no-distracting-elements */}
      <div className="marquee-container">
        {/*eslint-disable-next-line jsx-a11y/no-distracting-elements */}
        <marquee
          behavior="scroll"
          direction="left"
          className="marquee"
          onMouseOver={(e) => {
            e.target.stop();
          }}
          onMouseOut={(e) => {
            e.target.start();
          }}
        >
          {/* {updates.join(" | ")} */}
          {/* <span>
            <img
              id="ContentPlaceHolder1_Gridlatestannoc_imgNew_7"
              src={NewGIF}
              alt="error_gif"
            />
          </span> */}
          &nbsp;The results were updated after the Nov/Dec - 2023 Diploma
          Examinations
        </marquee>
      </div>
    </div>
  );
};

export default Navbar;
