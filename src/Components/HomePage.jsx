import React, { useState, useRef, useEffect } from "react";
import axios from "axios";
import html2canvas from "html2canvas";
import jsPDF from "jspdf";

const HomePage = () => {
  // State variables
  const [loading, setLoading] = useState(false);
  const [pdfLoading, setPdfLoading] = useState(false);
  const [fetchedData, setFetchedData] = useState([]);
  const [overallResult, setOverallResult] = useState(null);
  const [formData, setFormData] = useState({
    RegNo: "",
    Sem: "",
  });

  // Ref for the container div to be converted to PDF
  const containerRef = useRef(null);

  // Define the list of semesters
  const semesters = [1, 2, 3, 4, 5, 6];

  // Event handler for input change
  const handleInputChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value,
    });
  };

  // Event handler for form submission
  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true); // Set loading to true while fetching data

    try {
      const response = await axios.get("https://website-name/bte/Results.php", {
        params: {
          RegNo: formData.RegNo,
          Sem: formData.Sem,
        },
      });

      setFetchedData(response.data);
      console.log("Fetched data:", response.data);

      // Calculate overall result based on criteria
      const semesterCriteria = criteria[formData.Sem];
      const hasFailedSubject = response.data.some((result) =>
        isSubjectFailed(result, semesterCriteria)
      );

      setOverallResult(hasFailedSubject ? "Fail" : "Pass");
    } catch (error) {
      console.error("Error fetching data:", error);
    } finally {
      setLoading(false); // Set loading to false after data is fetched
    }
  };

  // Helper function to calculate the grand total of marks
  const calculateGrandTotal = () => {
    const grandTotal = fetchedData.reduce(
      (sum, result) =>
        sum +
        (result.exam_marks !== null ? parseInt(result.exam_marks) : 0) +
        (result.ia_marks !== null ? parseInt(result.ia_marks) : 0),
      0
    );
    return grandTotal;
  };

  // Helper function to calculate the percentage
  const calculatePercentage = (semester, grandTotal) => {
    let totalMarks;
    switch (semester) {
      case 1:
        totalMarks = 450;
        break;
      case 2:
        totalMarks = 550;
        break;
      case 3:
        totalMarks = 450;
        break;
      case 4:
        totalMarks = 450;
        break;
      case 5:
        totalMarks = 400;
        break;
      case 6:
        totalMarks = 400;
        break;
      default:
        return null; // Handle invalid semester
    }
    const percentage = (grandTotal / totalMarks) * 100;
    return percentage.toFixed(2); // Convert to string with 2 decimal points
  };

  // Helper function to check if a subject is failed based on criteria
  const isSubjectFailed = (result, semesterCriteria) => {
    const { subject_code, exam_marks, ia_marks } = result;
    const { exam, ia } = semesterCriteria[subject_code] || {};
    return (
      (exam && parseInt(exam_marks) < exam) || (ia && parseInt(ia_marks) < ia)
    );
  };

  // Helper function to get the result of an individual subject
  const getResult = (result) => {
    const semesterCriteria = criteria[parseInt(result.semester_number)];
    const { exam_marks, ia_marks } = result;

    if (!semesterCriteria || !semesterCriteria[result.subject_code]) {
      return "";
    }

    const { exam, ia } = semesterCriteria[result.subject_code];
    const examMarks = parseInt(exam_marks || 0);
    const iaMarks = parseInt(ia_marks || 0);

    if ((exam && examMarks < exam) || (ia && iaMarks < ia)) {
      return "F";
    } else {
      return "P";
    }
  };

  // Helper function to calculate common cell style based on the result
  const commonCellStyle = (result) => {
    const { semester_number, subject_code, exam_marks, ia_marks } = result;
    const semesterCriteria = criteria[parseInt(semester_number)];

    if (!semesterCriteria || !semesterCriteria[subject_code]) {
      return {};
    }

    const { exam, ia } = semesterCriteria[subject_code];
    const examMarks = parseInt(exam_marks);
    const iaMarks = parseInt(ia_marks);

    const isFailed = (exam && examMarks < exam) || (ia && iaMarks < ia);

    return {
      color: isFailed ? "red" : "inherit",
    };
  };

  // Helper function to get the style for the subject cell
  const getSubjectCellStyle = (result, criteria) => {
    const { semester_number, subject_code, exam_marks, ia_marks } = result;
    const semester = parseInt(semester_number);
    const semesterCriteria = criteria[semester];

    if (!semesterCriteria || !semesterCriteria[subject_code]) {
      return "inherit"; // If criteria is not defined, inherit default color
    }

    const { exam, ia } = semesterCriteria[subject_code];
    const examMarks = parseInt(exam_marks);
    const iaMarks = parseInt(ia_marks);

    if ((exam && examMarks < exam) || (ia && iaMarks < ia)) {
      return "red";
    } else {
      return "green";
    }
  };

  // Function to generate PDF from the captured HTML content
  const generatePdf = () => {
    setPdfLoading(true);

    // Display a note to users
    alert("If the PDF is not opening, please try using different PDF viewers.");

    // Get the container element
    const element = containerRef.current;
    const width = 1040;
    const height = 1095;

    // Capture the HTML content of the targeted div
    html2canvas(element, {
      scale: 2,
      windowWidth: width,
      windowHeight: height,
    }).then((canvas) => {
      try {
        // Increase the scale here
        const imgData = canvas.toDataURL("image/png");
        const pdf = new jsPDF("p", "mm", "a4"); // Specify A4 size in millimeters

        const imgWidth = pdf.internal.pageSize.getWidth();
        const imgHeight = (canvas.height * imgWidth) / canvas.width;

        // Calculate positioning to center content vertically
        const marginTop = (pdf.internal.pageSize.getHeight() - imgHeight) / 2;

        // Add header with date and time on top left corner and website title on top right corner
        const currentDate = new Date().toLocaleDateString();
        const currentTime = new Date().toLocaleTimeString();
        const websiteTitle = document.title;
        const header = `Date: ${currentDate} ${currentTime} | ${websiteTitle}`;
        pdf.setFontSize(10);
        pdf.text(header, 10, 13, {
          align: "left",
        });

        // Add footer with full website address in bottom left corner and page number on bottom right corner
        const websiteAddress = window.location.href;
        const footer = `Website : ${websiteAddress}`;
        pdf.text(footer, 10, pdf.internal.pageSize.getHeight() - 10, {
          align: "left",
        });
        const currentPage = pdf.internal.getNumberOfPages();
        pdf.text(
          `Page ${currentPage}`,
          pdf.internal.pageSize.getWidth() - 50,
          pdf.internal.pageSize.getHeight() - 10,
          {
            align: "right",
          }
        );

        // Add the captured content to the PDF, positioned in the middle
        pdf.addImage(imgData, "PNG", 0, marginTop, imgWidth, imgHeight);
        pdf.save(
          `Result_${fetchedData[0].register_number}(${fetchedData[0].semester_number}).pdf`
        );
        setPdfLoading(false);
      } catch (error) {
        console.error("Error generating PDF:", error);
        setPdfLoading(false);
        // Handle error appropriately (e.g., show error message to user)
      }
    });
  };

  // Define criteria for each subject and semester
  const criteria = {
    1: {
      // Criteria for semester 1 subjects

      // THEORY
      "20SC01T": { exam: 20, ia: 20 }, // CS, ME, EE
      "20CS11T": { exam: 20, ia: 20 }, // CS
      "20ME11T": { exam: 20, ia: 20 }, // ME
      "20EC11T": { exam: 20, ia: 20 }, // EC
      "20CE11T": { exam: 20, ia: 20 }, // CE
      "20EE11T": { exam: 20, ia: 20 }, // EE
      // PRACTICAL
      "20EC01P": { exam: 16, ia: 24 }, // CS, EC
      "20CS01P": { exam: 16, ia: 24 }, // CS, CE
      "20EG01P": { exam: 16, ia: 24 }, // ME, CE
      "20ME12P": { exam: 16, ia: 24 }, // ME
      "20SC02P": { exam: 16, ia: 24 }, // EC, CE
      "20ME02P": { exam: 16, ia: 24 }, // EC, EE
      "20EE01P": { exam: 16, ia: 24 }, // EE
      // AUDIT
      "20AU01T": { ia: 20 }, // CS, ME, EC, CE, EE
    },
    2: {
      // Criteria for semester 2 subjects

      // THEORY
      "20PM01T": { exam: 20, ia: 20 }, // CS, ME, EC, CE, EE
      "20SC01T": { exam: 20, ia: 20 }, // EC, CE
      // PRACTICAL
      "20SC02P": { exam: 16, ia: 24 }, // CS, ME, EE
      "20EG01P": { exam: 16, ia: 24 }, // CS, EC, EE
      "20ME02P": { exam: 16, ia: 24 }, // CS
      "20CS21P": { exam: 16, ia: 24 }, // CS
      "20EE01P": { exam: 16, ia: 24 }, // ME, CE
      "20CS01P": { exam: 16, ia: 24 }, // ME, EC, EE
      "20ME21P": { exam: 16, ia: 24 }, // ME
      "20EC21P": { exam: 16, ia: 24 }, // EC
      "20CE21P": { exam: 16, ia: 24 }, // CE
      "20CE22P": { exam: 16, ia: 24 }, // CE
      "20EE21P": { exam: 16, ia: 24 }, // EE
      // AUDIT
      "20KA21T": { ia: 20 }, // CS, ME, EC, CE, EE
    },
    3: {
      // Criteria for semester 3 subjects

      // PRACTICAL
      "20CS31P": { exam: 16, ia: 24 }, // CS
      "20CS32P": { exam: 16, ia: 24 }, // CS
      "20CS33P": { exam: 16, ia: 24 }, // CS
      "20CS34P": { exam: 16, ia: 24 }, // CS
      "20ME31P": { exam: 16, ia: 24 }, // ME
      "20ME32P": { exam: 16, ia: 24 }, // ME
      "20ME33P": { exam: 16, ia: 24 }, // ME
      "20ME34P": { exam: 16, ia: 24 }, // ME
      "20EC31P": { exam: 16, ia: 24 }, // EC
      "20EC32P": { exam: 16, ia: 24 }, // EC
      "20EC33P": { exam: 16, ia: 24 }, // EC
      "20EC34P": { exam: 16, ia: 24 }, // EC
      "20CE31P": { exam: 16, ia: 24 }, // CE
      "20CE32P": { exam: 16, ia: 24 }, // CE
      "20CE33P": { exam: 16, ia: 24 }, // CE
      "20CE34P": { exam: 16, ia: 24 }, // CE
      "20EE31P": { exam: 16, ia: 24 }, // EE
      "20EE32P": { exam: 16, ia: 24 }, // EE
      "20EE33P": { exam: 16, ia: 24 }, // EE
      "20EE34P": { exam: 16, ia: 24 }, // EE
      // AUDIT
      "20KA31T": { ia: 20 }, // CS, ME, EC, CE, EE
    },
    4: {
      // Criteria for semester 4 subjects

      // PRACTICAL
      "20CS41P": { exam: 16, ia: 24 }, // CS
      "20CS42P": { exam: 16, ia: 24 }, // CS
      "20CS43P": { exam: 16, ia: 24 }, // CS
      "20CS44P": { exam: 16, ia: 24 }, // CS
      "20ME41P": { exam: 16, ia: 24 }, // ME
      "20ME42P": { exam: 16, ia: 24 }, // ME
      "20ME43P": { exam: 16, ia: 24 }, // ME
      "20ME44P": { exam: 16, ia: 24 }, // ME
      "20EC41P": { exam: 16, ia: 24 }, // EC
      "20EC42P": { exam: 16, ia: 24 }, // EC
      "20EC43P": { exam: 16, ia: 24 }, // EC
      "20EC44P": { exam: 16, ia: 24 }, // EC
      "20CE41P": { exam: 16, ia: 24 }, // CE
      "20CE42P": { exam: 16, ia: 24 }, // CE
      "20CE43P": { exam: 16, ia: 24 }, // CE
      "20CE44P": { exam: 16, ia: 24 }, // CE
      "20EE41P": { exam: 16, ia: 24 }, // EE
      "20EE42P": { exam: 16, ia: 24 }, // EE
      "20EE43P": { exam: 16, ia: 24 }, // EE
      "20EE44P": { exam: 16, ia: 24 }, // EE
      // AUDIT
      "20CS45T": { ia: 20 }, // CS
      "20ME45T": { ia: 20 }, // ME
      "20EC45T": { ia: 20 }, // EC
      "20CE45T": { ia: 20 }, // CE
      "20EE45T": { ia: 20 }, // EE
    },
    5: {
      // Criteria for semester 5 subjects

      // Programme Specialization Pathway
      "20CS51I": { exam: 64, ia: 96 }, // CS
      "20CS52I": { exam: 64, ia: 96 }, // CS
      "20CS53I": { exam: 64, ia: 96 }, // CS
      "20CS54I": { exam: 64, ia: 96 }, // CS

      "20ME51I": { exam: 64, ia: 96 }, // ME
      "20ME52I": { exam: 64, ia: 96 }, // ME
      "20ME53I": { exam: 64, ia: 96 }, // ME
      "20ME54I": { exam: 64, ia: 96 }, // ME

      "20EC51I": { exam: 64, ia: 96 }, // EC
      "20EC52I": { exam: 64, ia: 96 }, // EC
      "20EC53I": { exam: 64, ia: 96 }, // EC
      "20EC54I": { exam: 64, ia: 96 }, // EC

      "20CE51I": { exam: 64, ia: 96 }, // CE
      "20CE52I": { exam: 64, ia: 96 }, // CE
      "20CE53I": { exam: 64, ia: 96 }, // CE
      "20CE54I": { exam: 64, ia: 96 }, // CE

      "20EE51I": { exam: 64, ia: 96 }, // EE
      "20EE52I": { exam: 64, ia: 96 }, // EE
      "20EE53I": { exam: 64, ia: 96 }, // EE
      "20EE54I": { exam: 64, ia: 96 }, // EE

      "20SC51T": { exam: 20, ia: 20 }, // CS, ME, EC, CE, EE
      "20SC52T": { exam: 20, ia: 20 }, // CS, ME, EC, CE, EE
      "20RM53T": { exam: 20, ia: 20 }, // CS, ME, EC, CE, EE
      "20TW54P": { exam: 16, ia: 24 }, // CS, ME, EC, CE, EE
      "20ET51I": { exam: 64, ia: 96 }, // CS, ME, EC, CE, EE
    },
    6: {
      // Criteria for semester 6 subjects

      // Specialization Pathway
      "20CS61S": { exam: 64, ia: 96 }, // CS
      "20CS61R": { exam: 64, ia: 96 }, // CS
      "20CS61E": { exam: 64, ia: 96 }, // CS

      "20ME61S": { exam: 64, ia: 96 }, // ME
      "20ME61R": { exam: 64, ia: 96 }, // ME
      "20ME61E": { exam: 64, ia: 96 }, // ME

      "20EC61S": { exam: 64, ia: 96 }, // EC
      "20EC61R": { exam: 64, ia: 96 }, // EC
      "20EC61E": { exam: 64, ia: 96 }, // EC

      "20CE61S": { exam: 64, ia: 96 }, // CE
      "20CE61R": { exam: 64, ia: 96 }, // CE
      "20CE61E": { exam: 64, ia: 96 }, // CE

      "20EE61S": { exam: 64, ia: 96 }, // EE
      "20EE61R": { exam: 64, ia: 96 }, // EE
      "20EE61E": { exam: 64, ia: 96 }, // EE
    },
  };

  // Conditional rendering for mobile note
  const [isMobile, setIsMobile] = useState(false);

  useEffect(() => {
    const checkIsMobile = () => {
      setIsMobile(window.innerWidth < 768); // Adjust breakpoint as needed
    };

    checkIsMobile(); // Check initially
    window.addEventListener("resize", checkIsMobile); // Check on resize

    return () => {
      window.removeEventListener("resize", checkIsMobile); // Clean up listener
    };
  }, []);

  // Render UI components
  return (
    <div>
      {isMobile && (
        <div className="container mt-5">
          <div
            className="alert alert-warning alert-dismissible fade show"
            role="alert"
          >
            Please switch to the Desktop Site for a better experience.
            <button
              type="button"
              className="btn-close"
              data-bs-dismiss="alert"
              aria-label="Close"
            ></button>
          </div>
        </div>
      )}
      <div className="container my-5 border rounded p-4 bg-light">
        <form className="row justify-content-center" onSubmit={handleSubmit}>
          <div className="col-md-6 mb-3">
            <label className="form-label" htmlFor="reg_no">
              Register No.
            </label>
            <input
              type="text"
              className="form-control"
              id="reg_no"
              placeholder="Enter Register No."
              name="RegNo"
              value={formData.RegNo}
              onChange={handleInputChange}
              required
            />
          </div>
          <div className="col-md-6 mb-3">
            <label className="form-label" htmlFor="s_sem">
              Semester
            </label>
            <select
              className="form-select"
              id="s_sem"
              aria-label="Select Semester"
              name="Sem"
              value={formData.Sem}
              onChange={handleInputChange}
              required
            >
              <option defaultValue>Select Semester</option>
              {semesters.map((Sem) => (
                <option key={Sem} value={Sem}>
                  {Sem}
                </option>
              ))}
            </select>
          </div>
          <div className="col-md-12 text-center">
            <button type="submit" className="btn btn-primary">
              {loading ? "Loading..." : "Get Results"}
            </button>
          </div>
        </form>

        {Array.isArray(fetchedData) && fetchedData.length > 0 && (
          <div className="mt-3 container border rounded p-4 shadow bg-white">
            <div className="border p-3" ref={containerRef}>
              <div className="border rounded p-3 bg-primary-subtle">
                <p className="lead">Student Information</p>
                <p>
                  <strong>Register Number : </strong>{" "}
                  <i>{fetchedData[0].register_number}</i>
                </p>
                <p>
                  <strong>Student Name : </strong>{" "}
                  <i>{fetchedData[0].student_name}</i>
                </p>
                <p>
                  <strong>Father Name : </strong>{" "}
                  <i>{fetchedData[0].father_name}</i>
                </p>
                <p>
                  <strong>Branch : </strong> <i>{fetchedData[0].branch_name}</i>
                </p>
                <p>
                  <strong>Semester : </strong>{" "}
                  <i>{fetchedData[0].semester_number}</i>
                </p>
                <p>
                  <strong>Institute : </strong>
                  <i>
                    {fetchedData[0].institute_id} -{" "}
                    {fetchedData[0].institute_name}
                  </i>
                </p>
              </div>
              <div className="table-responsive mt-3 border p-3">
                <table className="table table-striped table-bordered table-hover">
                  <thead className="thead-light">
                    <tr className="table-dark text-center align-middle">
                      <th scope="col">Sl.No.</th>
                      <th scope="col">Subject Name</th>
                      <th scope="col">Exam Marks</th>
                      <th scope="col">IA Marks</th>
                      <th scope="col">Total Marks</th>
                      <th scope="col">Subject Code</th>
                      <th scope="col">Results</th>
                    </tr>
                  </thead>
                  <tbody>
                    {fetchedData.map((result, index) => (
                      <tr key={index}>
                        <td
                          style={commonCellStyle(result)}
                          className="text-center align-middle"
                        >
                          {index + 1}
                        </td>
                        <td
                          style={commonCellStyle(result)}
                          className="align-middle"
                        >
                          {result.subject_name}
                        </td>
                        <td
                          style={commonCellStyle(result)}
                          className="text-center align-middle"
                        >
                          {result.exam_marks ? result.exam_marks : "--"}
                        </td>
                        <td
                          style={commonCellStyle(result)}
                          className="text-center align-middle"
                        >
                          {result.ia_marks}
                        </td>
                        <td
                          style={commonCellStyle(result)}
                          className="text-center align-middle"
                        >
                          {(() => {
                            const examMarks =
                              result.exam_marks !== null
                                ? parseInt(result.exam_marks)
                                : 0;
                            const iaMarks =
                              result.ia_marks !== null
                                ? parseInt(result.ia_marks)
                                : 0;

                            return examMarks + iaMarks;
                          })()}
                        </td>
                        <td
                          style={commonCellStyle(result)}
                          className="text-center align-middle"
                        >
                          {result.subject_code}
                        </td>
                        <td
                          className="text-center align-middle"
                          style={{
                            color: getSubjectCellStyle(result, criteria),
                          }}
                        >
                          {getResult(result)}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>

              <div className="mt-3 border rounded p-3 bg-primary-subtle">
                <p>
                  <strong>Total Exam Marks : </strong>{" "}
                  <i>
                    {(() => {
                      const totalExamMarks = fetchedData.reduce(
                        (sum, result) =>
                          sum +
                          (result.exam_marks !== null
                            ? parseInt(result.exam_marks)
                            : 0),
                        0
                      );
                      return totalExamMarks;
                    })()}
                  </i>
                </p>
                <p>
                  <strong>Total IA Marks : </strong>{" "}
                  <i>
                    {(() => {
                      const totalIAMarks = fetchedData.reduce(
                        (sum, result) =>
                          sum +
                          (result.ia_marks !== null
                            ? parseInt(result.ia_marks)
                            : 0),
                        0
                      );
                      return totalIAMarks;
                    })()}
                  </i>
                </p>
                <p>
                  <strong>Grand Total : </strong>
                  <i>{calculateGrandTotal()}</i>
                </p>
                <div>
                  {/* Conditionally render percentage based on overall result */}
                  {overallResult === "Pass" && (
                    <p>
                      <strong>Percentage : </strong>
                      <i>
                        {calculatePercentage(
                          parseInt(formData.Sem),
                          calculateGrandTotal()
                        )}
                        %
                      </i>
                    </p>
                  )}
                </div>
                {overallResult && (
                  <div>
                    <strong>
                      Overall Result :{" "}
                      <span
                        style={{
                          color: overallResult === "Pass" ? "green" : "red",
                        }}
                      >
                        <i>{overallResult}</i>
                      </span>
                    </strong>
                  </div>
                )}
              </div>
              <div className="container mt-4 px-0">
                <div className="alert alert-secondary" role="alert">
                  <p className="mb-0">
                    <strong>NOTE: </strong>
                    This is Provisional Results of C-20 Exams Only, The results
                    published on NET are for immediate information to the
                    examinees, These cannot be treated as original mark sheets,
                    Updates will be done and informed to the institutes. Any
                    Grievance/Issue write to:
                    <a
                      href="mailto:btelinx@gmail.com"
                      className="alert-link text-primary"
                    >
                      btelinx@gmail.com
                    </a>
                  </p>
                </div>
              </div>
            </div>
            <button
              onClick={generatePdf}
              disabled={pdfLoading}
              className="btn btn-primary my-3 w-100"
            >
              {pdfLoading ? "Generating PDF..." : "Download as PDF"}
            </button>
          </div>
        )}
      </div>
    </div>
  );
};

export default HomePage;
