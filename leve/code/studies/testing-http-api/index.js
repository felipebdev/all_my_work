module.exports.handler = async (event) => {
  return {
    statusCode: 200,
    body: JSON.stringify(
      {
        message: "Testing HTTP Api Serverless",
        input: event,
      },
      null,
      2
    ),
  };
};
