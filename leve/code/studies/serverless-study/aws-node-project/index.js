module.exports.handler = async (event) => {
  return {
    statusCode: 200,
    body: JSON.stringify(
      {
        message: 'Function1 invoked successfully!',
        input: event,
      },
      null,
      2
    ),
  };
};