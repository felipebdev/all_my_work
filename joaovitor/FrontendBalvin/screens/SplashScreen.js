import { StyleSheet, Text, View, Image } from 'react-native'
import React from 'react'

const SplashScreen = ({navigation}) => {
    setTimeout(() => {
        navigation.navigate('Login')
    }, 3500);
    
  return (
    <View style={{width: '100%', height: '100%', justifyContent: 'center', alignItems: 'center', backgroundColor: 'white'}}>
        <Image source={require('../images/logobalvin.png')} style={{width: 200, height: 200}}/>
    </View>
  )
}

export default SplashScreen

const styles = StyleSheet.create({})