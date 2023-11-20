import { StyleSheet, Text, View, Image } from 'react-native'
import React from 'react'
import {createBottomTabNavigator} from '@react-navigation/bottom-tabs';
import Homescreen from '../screens/Homescreen';
import Pesquisar from '../screens/Pesquisar';
import Perfil from '../screens/Perfil';
import Addpost from './Addpost';
import Notificacao from './Notificacao';

const Tab = createBottomTabNavigator();

const Menu = () => {
  
  return (
    <Tab.Navigator tabBarOptions={{ showLabel: false}} 
    
    screenOptions={{
      tabBarStyle: {height: 60, alignItems: 'center'}}}
    >
        <Tab.Screen name='Homescreen' component={Homescreen} options={{headerShown: false, tabBarIcon: 
          ({size, focused, color}) => {
            return(
              <Image source={require('../images/home.png')} style={{tintColor: focused ? '#ec230d' : "#4E4E4E" }}/>
            )
          }}}/>

        <Tab.Screen name='Pesquisar' component={Pesquisar}  options={{headerShown: false, tabBarIcon: 
          ({size, focused, color}) => {
            return(
              <Image source={require('../images/pesquisar.png')} style={{tintColor: focused ? '#ec230d' : "#4E4E4E" }}/>
            )
          }}}/>


          <Tab.Screen name='Addpost' component={Addpost}  options={{headerShown: false, tabBarIcon: 
          ({size, focused, color}) => {
            return(
              <View style={{width: 60, height: 60, borderRadius: 150, alignItems: 'center', marginTop: -40, 
              justifyContent: 'center', backgroundColor: focused ? '#FAFAFA' : '#ec230d'}}>
              <Image source={require('../images/add.png')} style={{width: 30, height: 30, tintColor: focused ? '#ec230d' : "white"}}/>
            </View>
            )
          }}}/>

        <Tab.Screen name='notificacao' component={Notificacao}  options={{headerShown: false, tabBarIcon: 
          ({size, focused, color}) => {
            return(
             
              <Image source={require('../images/notificacao.png')} style={{tintColor: focused ? '#ec230d' : "#4E4E4E"}}/>
         
            )
          }}}/>

        <Tab.Screen name='Perfil' component={Perfil}  options={{headerShown: false, tabBarIcon: 
          ({size, focused, color}) => {
            return(
              <Image source={require('../images/userpic.png')} style={{width: 20, height: 19, tintColor: focused ? '#ec230d' : "#4E4E4E" }}/>
            )
          }}}/>

    </Tab.Navigator>
  )
}

export default Menu

const styles = StyleSheet.create({})